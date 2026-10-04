<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_moving_a_task_requires_its_existing_assignee_to_belong_to_the_new_project(): void
    {
        Sanctum::actingAs($this->person());
        $staff = $this->person('staff');
        $original = $this->project();
        $original->members()->attach($staff);
        $destination = $this->project();
        $task = Task::create(['project_id' => $original->id, 'assignee_id' => $staff->id, 'title' => 'Assigned']);
        $this->patchJson('/api/tasks/'.$task->id, ['project_id' => $destination->id])
            ->assertUnprocessable()->assertJsonValidationErrors('assignee_id');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'project_id' => $original->id]);
        $this->patchJson('/api/tasks/'.$task->id, ['project_id' => $destination->id, 'assignee_id' => null])
            ->assertOk()->assertJsonPath('data.assignee_id', null);
    }

    public function test_task_lists_and_comment_access_follow_project_membership(): void
    {
        $staff = $this->person('staff');
        $visible = $this->project();
        $visible->members()->attach($staff);
        $hidden = $this->project();
        $task = Task::create(['project_id' => $visible->id, 'title' => 'Visible']);
        $secret = Task::create(['project_id' => $hidden->id, 'title' => 'Secret']);
        Sanctum::actingAs($staff);
        $this->getJson('/api/tasks')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/tasks/'.$secret->id)->assertNotFound();
        $this->postJson('/api/tasks/'.$secret->id.'/comments', ['body' => 'No access'])->assertForbidden();
        $this->postJson('/api/tasks/'.$task->id.'/comments', ['body' => 'Update'])
            ->assertCreated()->assertJsonPath('data.user.id', $staff->id)->assertJsonMissingPath('data.user.password');
        $this->getJson('/api/tasks/'.$task->id)->assertOk()->assertJsonCount(1, 'data.comments');
        $this->getJson('/api/activity?task_id='.$task->id)->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/activity?task_id=invalid')->assertUnprocessable();
    }
}
