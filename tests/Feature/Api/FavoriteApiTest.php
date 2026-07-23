<?php

namespace Tests\Feature\Api;

use App\Models\Favorite;
use App\Models\FavoriteFolder;
use App\Models\Project;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class FavoriteApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_favorite_folders_and_items_support_the_complete_workflow(): void
    {
        $user = User::factory()->create();
        $this->withToken(app(JwtService::class)->issue($user));

        $folderId = $this->postJson('/api/v1/favorite-folders', ['name' => 'Hotel Projects', 'icon' => 'hotel'])
            ->assertCreated()->assertJsonPath('data.items_count', 0)->json('data.id');

        $favoriteId = $this->postJson('/api/v1/favorites', [
            'favorite_folder_id' => $folderId,
            'type' => 'texture',
            'name' => 'Venetian Velvet',
            'collection' => 'Hotel Projects',
            'material' => 'Heavy Weight Polyester',
            'image_url' => 'https://example.com/velvet.jpg',
            'colors' => ['#005D3B', '#351B1D'],
        ])->assertCreated()->assertJsonPath('data.folder.name', 'Hotel Projects')->json('data.id');

        $this->getJson("/api/v1/favorites?folder={$folderId}")->assertOk()->assertJsonCount(1, 'data');
        $project = Project::factory()->create(['user_id' => $user->id, 'fabrics' => []]);
        $this->postJson("/api/v1/favorites/{$favoriteId}/projects", ['project_id' => $project->id])
            ->assertOk()->assertJsonPath('data.id', $favoriteId);
        $this->assertSame('Venetian Velvet', $project->refresh()->fabrics[0]['name']);

        $this->postJson("/api/v1/favorites/{$favoriteId}/sample-request")
            ->assertOk()->assertJsonPath('data.id', $favoriteId);
        $this->assertNotNull(Favorite::findOrFail($favoriteId)->sample_requested_at);

        $favorite = Favorite::findOrFail($favoriteId);
        $this->deleteJson("/api/v1/favorites/{$favoriteId}")->assertNoContent();
        $this->assertModelMissing($favorite);

        $folder = FavoriteFolder::findOrFail($folderId);
        $this->deleteJson("/api/v1/favorite-folders/{$folderId}")->assertNoContent();
        $this->assertModelMissing($folder);
    }

    public function test_favorite_validation_rejects_invalid_colors_and_image_urls(): void
    {
        $user = User::factory()->create();
        $this->withToken(app(JwtService::class)->issue($user));

        $this->postJson('/api/v1/favorites', [
            'name' => 'Invalid', 'type' => 'invalid', 'collection' => 'Test', 'material' => 'Test',
            'image_url' => 'not-a-url', 'colors' => ['green'],
        ])->assertUnprocessable()->assertJsonValidationErrors(['type', 'image_url', 'colors.0']);
    }

    public function test_favorites_require_authentication_and_are_isolated_by_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $favorite = Favorite::factory()->create(['user_id' => $owner->id]);
        $folder = FavoriteFolder::factory()->create(['user_id' => $owner->id]);

        $this->getJson('/api/v1/favorites')->assertUnauthorized();

        $this->withToken(app(JwtService::class)->issue($otherUser));
        $this->getJson('/api/v1/favorites')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/favorites/{$favorite->id}")->assertNotFound();
        $this->deleteJson("/api/v1/favorite-folders/{$folder->id}")->assertNotFound();
    }
}
