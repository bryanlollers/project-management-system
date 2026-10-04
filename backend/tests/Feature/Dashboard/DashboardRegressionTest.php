<?php

namespace Tests\Feature\Dashboard;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class DashboardRegressionTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_filter_pagination_dashboard_and_client_delete_guard(): void
    {
        Sanctum::actingAs($this->person());
        $p = $this->project();
        Task::create(['title' => 'Overdue', 'project_id' => $p->id, 'due_date' => today()->subDay(), 'status' => 'todo']);
        $this->getJson('/api/projects?status=active&search=Test&per_page=1')->assertOk()->assertJsonPath('meta.total', 1);
        $this->getJson('/api/dashboard')->assertOk()->assertJsonPath('overdue_tasks', 1);
        $this->deleteJson('/api/clients/'.$p->client_id)->assertUnprocessable();
        $this->deleteJson('/api/projects/'.$p->id)->assertNoContent();
        $this->assertDatabaseCount('tasks', 0);
    }
}
