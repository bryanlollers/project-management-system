<?php

namespace Tests\Feature\Dashboard;

use App\Models\Activity;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_staff_reports_and_activity_exclude_non_member_projects(): void
    {
        $staff = $this->person('staff');
        $visible = $this->project();
        $visible->members()->attach($staff);
        $hidden = $this->project();
        Task::create(['project_id' => $visible->id, 'assignee_id' => $staff->id, 'title' => 'Visible overdue', 'due_date' => today()->subDay()]);
        Task::create(['project_id' => $hidden->id, 'assignee_id' => $staff->id, 'title' => 'Hidden overdue', 'due_date' => today()->subDay()]);
        Activity::create(['user_id' => $staff->id, 'project_id' => $visible->id, 'description' => 'Visible']);
        Activity::create(['user_id' => $staff->id, 'project_id' => $hidden->id, 'description' => 'Hidden']);
        Sanctum::actingAs($staff);
        $this->getJson('/api/dashboard')->assertOk()->assertJsonMissingPath('data')
            ->assertJsonPath('active_projects', 1)->assertJsonPath('total_tasks', 1)->assertJsonPath('overdue_tasks', 1)
            ->assertJsonPath('workload.0.tasks_count', 1)->assertJsonPath('task_status.0.total', 1);
        $this->getJson('/api/activity')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.description', 'Visible');
    }
}
