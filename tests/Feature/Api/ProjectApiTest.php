<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->withToken(app(JwtService::class)->issue($this->user));
    }

    public function test_projects_can_be_created_filtered_updated_and_deleted(): void
    {
        $response = $this->postJson('/api/v1/projects', [
            'name' => 'Hotel Lobby',
            'client' => 'Marriott International',
            'status' => 'active',
            'fabrics' => [['name' => 'Royal Velvet', 'collection' => 'Luxe', 'color' => '#7A5900', 'image' => '/storage/colors/royal-velvet.webp']],
            'saved_colors' => [['name' => 'Antique Gold', 'hex' => '#7A5900']],
            'notes' => ['Approve sample.'],
            'members' => [['name' => 'Amelia Stone', 'role' => 'Designer', 'initials' => 'AS']],
            'timeline' => [['title' => 'Approval', 'date' => '2026-08-01', 'completed' => false]],
            'recent_activity' => [['text' => 'Project created', 'time' => 'Now']],
        ]);

        $response->assertCreated()->assertJsonPath('data.fabrics_count', 1)
            ->assertJsonPath('data.fabrics.0.image', '/storage/colors/royal-velvet.webp')
            ->assertJsonPath('data.saved_colors.0.name', 'Antique Gold')
            ->assertJsonPath('data.saved_colors.0.hex', '#7A5900');
        $project = Project::firstOrFail();

        $this->getJson('/api/v1/projects?status=active&search=Hotel')
            ->assertOk()->assertJsonCount(1, 'data');
        $this->patchJson("/api/v1/projects/{$project->id}/favorite")
            ->assertOk()->assertJsonPath('data.is_favorite', true);
        $this->putJson("/api/v1/projects/{$project->id}", [
            'name' => 'Hotel Lobby Revised', 'client' => 'Marriott International', 'status' => 'in_review',
        ])->assertOk()->assertJsonPath('data.status', 'in_review');
        $this->deleteJson("/api/v1/projects/{$project->id}")->assertNoContent();
        $this->assertModelMissing($project);
    }

    public function test_project_can_be_duplicated_and_archived(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id, 'name' => 'Villa Living Room']);

        $this->postJson("/api/v1/projects/{$project->id}/duplicate")
            ->assertCreated()->assertJsonPath('data.name', 'Villa Living Room — Copy');
        $this->assertDatabaseCount('projects', 2);

        $this->patchJson("/api/v1/projects/{$project->id}/archive")
            ->assertOk()->assertJsonPath('data.id', $project->id);
        $this->getJson('/api/v1/projects')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/projects?include_archived=1')
            ->assertOk()->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.archived_at', fn ($value) => is_string($value));
    }

    public function test_project_validation_rejects_invalid_nested_content(): void
    {
        $this->postJson('/api/v1/projects', [
            'name' => 'Office', 'client' => 'Client', 'status' => 'unknown',
            'fabrics' => [['collection' => 'Missing name']],
        ])->assertUnprocessable()->assertJsonValidationErrors(['status', 'fabrics.0.name']);
    }

    public function test_project_images_are_uploaded_and_projects_are_private_to_the_owner(): void
    {
        Storage::fake('public');

        $projectId = $this->post('/api/v1/projects', [
            'name' => 'Restaurant',
            'client' => 'Hospitality Group',
            'status' => 'active',
            'cover_image_file' => UploadedFile::fake()->image('cover.jpg'),
        ])->assertCreated()->json('data.id');

        $project = Project::findOrFail($projectId);
        Storage::disk('public')->assertExists($project->cover_image);

        $this->post("/api/v1/projects/{$projectId}/inspiration", [
            'image' => UploadedFile::fake()->image('moodboard.png'),
        ])->assertOk()->assertJsonCount(1, 'data.inspiration_images');
        Storage::disk('public')->assertExists($project->refresh()->inspiration_images[0]);

        $otherUser = User::factory()->create();
        $this->withToken(app(JwtService::class)->issue($otherUser));
        $this->getJson("/api/v1/projects/{$projectId}")->assertNotFound();
    }
}
