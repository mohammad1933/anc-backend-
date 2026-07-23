<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminProjectManagementApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_only_admin_can_browse_all_projects_with_owner_details(): void
    {
        $owner = User::factory()->create(['name' => 'Interior Designer']);
        Project::factory()->create(['user_id' => $owner->id, 'name' => 'Hotel Lobby', 'fabrics' => [['name' => 'Velvet']]]);

        $this->getJson('/api/v1/admin/projects')->assertUnauthorized();

        $customer = User::factory()->create();
        $this->withToken(app(JwtService::class)->issue($customer));
        $this->getJson('/api/v1/admin/projects')->assertForbidden();

        $this->authenticateAsAdmin();
        $this->getJson('/api/v1/admin/projects?search=Designer')
            ->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.owner.name', 'Interior Designer')
            ->assertJsonPath('data.0.fabrics_count', 1);
    }

    public function test_admin_can_permanently_delete_a_project(): void
    {
        $project = Project::factory()->create();
        $this->authenticateAsAdmin();

        $this->deleteJson("/api/v1/admin/projects/{$project->id}")->assertNoContent();
        $this->assertModelMissing($project);
    }
}
