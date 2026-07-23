<?php

namespace Tests\Feature\Api;

use App\Models\Catalog;
use App\Models\Color;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ColorFavoriteApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_color_favorites_require_authentication(): void
    {
        $color = $this->createColor();

        $this->getJson('/api/v1/color-favorites')->assertUnauthorized();
        $this->patchJson("/api/v1/colors/{$color->id}/favorite")->assertUnauthorized();
    }

    public function test_user_can_toggle_and_list_color_favorites(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $color = $this->createColor();
        $this->withToken(app(JwtService::class)->issue($user));

        $this->patchJson("/api/v1/colors/{$color->id}/favorite")
            ->assertOk()
            ->assertJsonPath('data.color_id', $color->id)
            ->assertJsonPath('data.is_favorite', true);

        $this->getJson('/api/v1/color-favorites')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $color->id)
            ->assertJsonPath('data.0.catalog.name', 'Royal Velvet');

        $this->assertTrue($user->favoriteColors()->whereKey($color->id)->exists());
        $this->assertFalse($otherUser->favoriteColors()->whereKey($color->id)->exists());

        $this->patchJson("/api/v1/colors/{$color->id}/favorite")
            ->assertOk()
            ->assertJsonPath('data.is_favorite', false);

        $this->getJson('/api/v1/color-favorites')->assertOk()->assertJsonCount(0, 'data');
    }

    private function createColor(): Color
    {
        $catalog = Catalog::create([
            'name' => 'Royal Velvet',
            'slug' => 'royal-velvet',
            'status' => 'published',
        ]);

        return Color::create([
            'catalog_id' => $catalog->id,
            'name' => 'Ruby',
            'code' => 'RV-01',
            'sku' => 'RV-01',
            'hex_code' => '#991122',
        ]);
    }
}
