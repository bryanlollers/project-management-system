<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\CreatesWorkspaceFixtures;
use Tests\TestCase;

class ClientApiTest extends TestCase
{
    use CreatesWorkspaceFixtures, RefreshDatabase;

    public function test_client_contacts_crud_and_activity_contract(): void
    {
        Sanctum::actingAs($this->person());
        $response = $this->postJson('/api/clients', [
            'name' => 'Acme', 'email' => 'hello@acme.test',
            'contacts' => [['name' => 'Pat', 'email' => 'pat@acme.test']],
        ])->assertCreated()->assertJsonPath('data.contacts.0.name', 'Pat');
        $id = $response->json('data.id');
        $this->patchJson('/api/clients/'.$id, ['phone' => '123'])->assertOk()->assertJsonPath('data.phone', '123');
        $this->getJson('/api/clients?search=Acme&per_page=1')->assertOk()->assertJsonPath('meta.total', 1);
        $this->getJson('/api/clients/'.$id)->assertOk()->assertJsonPath('data.projects', []);
        $this->getJson('/api/activity')->assertOk()->assertJsonPath('data.0.description', 'Updated Acme');
        $this->deleteJson('/api/clients/'.$id)->assertNoContent();
        $this->assertDatabaseMissing('clients', ['id' => $id]);
    }

    public function test_staff_client_details_hide_other_projects(): void
    {
        $staff = $this->person('staff');
        $visible = $this->project();
        $visible->members()->attach($staff);
        $hidden = Project::create(['name' => 'Hidden', 'client_id' => $visible->client_id]);
        $hiddenClient = Client::create(['name' => 'Other', 'email' => 'other@example.com']);
        Sanctum::actingAs($staff);
        $this->getJson('/api/clients')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/clients/'.$visible->client_id)->assertOk()
            ->assertJsonCount(1, 'data.projects')->assertJsonPath('data.projects.0.id', $visible->id);
        $this->getJson('/api/clients/'.$hiddenClient->id)->assertNotFound();
        $this->patchJson('/api/clients/'.$visible->client_id, ['name' => 'Forbidden'])->assertForbidden();
        $this->deleteJson('/api/clients/'.$visible->client_id)->assertForbidden();
    }

    public function test_invalid_contacts_and_pagination_are_rejected(): void
    {
        Sanctum::actingAs($this->person());
        $this->postJson('/api/clients', ['name' => 'Bad', 'email' => 'bad@example.com', 'contacts' => [['name' => 'Pat']]])
            ->assertUnprocessable()->assertJsonValidationErrors('contacts.0.email');
        $this->getJson('/api/clients?per_page=101')->assertUnprocessable();
        $this->getJson('/api/clients/not-an-id')->assertNotFound();
    }
}
