<?php

namespace Tests\Feature\Projects;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_member_removal_failure_does_not_update_project_or_log_activity(): void
    {
        Sanctum::actingAs($this->person());
        $staff = $this->person('staff');
        $project = $this->project();
        $project->members()->attach($staff);
        Task::create(['project_id' => $project->id, 'assignee_id' => $staff->id, 'title' => 'Assigned']);
        $this->patchJson('/api/projects/'.$project->id, ['name' => 'Should not persist', 'member_ids' => []])
            ->assertUnprocessable()->assertJsonValidationErrors('member_ids');
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Test project']);
        $this->assertDatabaseHas('project_user', ['project_id' => $project->id, 'user_id' => $staff->id]);
        $this->assertDatabaseCount('activities', 0);
    }

    public function test_partial_date_updates_validate_the_merged_dates(): void
    {
        Sanctum::actingAs($this->person());
        $project = $this->project();
        $project->update(['start_date' => '2026-10-10', 'end_date' => '2026-10-20']);
        $this->patchJson('/api/projects/'.$project->id, ['start_date' => '2026-10-21'])
            ->assertUnprocessable()->assertJsonValidationErrors('end_date');
        $this->patchJson('/api/projects/'.$project->id, ['end_date' => '2026-10-09'])
            ->assertUnprocessable()->assertJsonValidationErrors('end_date');
        $this->patchJson('/api/projects/'.$project->id, ['end_date' => null])->assertOk();
    }

    public function test_project_resources_include_counts_progress_and_nested_members_without_passwords(): void
    {
        $admin = $this->person();
        Sanctum::actingAs($admin);
        $project = $this->project();
        $project->members()->attach($admin);
        Task::create(['project_id' => $project->id, 'assignee_id' => $admin->id, 'title' => 'Done', 'status' => 'done']);
        Task::create(['project_id' => $project->id, 'title' => 'Todo', 'status' => 'todo']);
        $this->getJson('/api/projects')->assertOk()->assertJsonPath('data.0.progress', 50)
            ->assertJsonPath('data.0.tasks_count', 2)->assertJsonPath('data.0.members.0.id', $admin->id)
            ->assertJsonMissingPath('data.0.members.0.password');
        $this->getJson('/api/projects/'.$project->id)->assertOk()->assertJsonCount(2, 'data.tasks')
            ->assertJsonPath('data.client.id', $project->client_id)
            ->assertJsonMissingPath('data.tasks.0.assignee.password');
    }
}
