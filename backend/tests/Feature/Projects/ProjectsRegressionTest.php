<?php

namespace Tests\Feature\Projects;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class ProjectsRegressionTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_staff_only_see_member_projects_and_cannot_create(): void
    {
        $staff = $this->person('staff');
        $visible = $this->project();
        $visible->members()->attach($staff);
        $hidden = $this->project();
        Sanctum::actingAs($staff);
        $this->getJson('/api/projects')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/projects/'.$hidden->id)->assertNotFound();
        $this->postJson('/api/projects', ['name' => 'New', 'client_id' => $visible->client_id])->assertForbidden();
        $this->patchJson('/api/projects/'.$visible->id, ['name' => 'Rename'])->assertForbidden();
    }

    public function test_validation_membership_and_progress(): void
    {
        Sanctum::actingAs($this->person());
        $p = $this->project();
        $staff = $this->person('staff');
        $this->postJson('/api/tasks', ['project_id' => $p->id, 'title' => 'Task', 'assignee_id' => $staff->id])->assertUnprocessable();
        $p->members()->attach($staff);
        $this->postJson('/api/tasks', ['project_id' => $p->id, 'title' => 'Task', 'assignee_id' => $staff->id, 'status' => 'done'])->assertCreated();
        $this->getJson('/api/projects/'.$p->id)->assertJsonPath('data.progress', 100);
        $this->patchJson('/api/projects/'.$p->id, ['member_ids' => []])->assertUnprocessable();
        $this->postJson('/api/projects', ['name' => 'Bad dates', 'client_id' => $p->client_id, 'start_date' => '2026-10-10', 'end_date' => '2026-10-01'])->assertUnprocessable();
        $this->postJson('/api/tasks', ['project_id' => $p->id, 'title' => 'Invalid', 'status' => 'invalid'])->assertUnprocessable();
    }
}
