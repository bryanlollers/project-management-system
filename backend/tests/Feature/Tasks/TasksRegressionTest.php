<?php

namespace Tests\Feature\Tasks;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class TasksRegressionTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_staff_can_only_change_their_task_status(): void
    {
        $staff = $this->person('staff');
        $other = $this->person('staff');
        $p = $this->project();
        $p->members()->attach([$staff->id, $other->id]);
        $mine = Task::create(['project_id' => $p->id, 'assignee_id' => $staff->id, 'title' => 'Mine']);
        $theirs = Task::create(['project_id' => $p->id, 'assignee_id' => $other->id, 'title' => 'Theirs']);
        Sanctum::actingAs($staff);
        $this->patchJson('/api/tasks/'.$mine->id, ['status' => 'done'])->assertOk();
        $this->patchJson('/api/tasks/'.$mine->id, ['title' => 'Changed'])->assertForbidden();
        $this->patchJson('/api/tasks/'.$theirs->id, ['status' => 'done'])->assertForbidden();
        $this->postJson('/api/tasks/'.$mine->id.'/comments', ['body' => 'Update'])->assertCreated();
        $this->assertDatabaseHas('activities', ['task_id' => $mine->id, 'description' => 'Commented on Mine']);
    }
}
