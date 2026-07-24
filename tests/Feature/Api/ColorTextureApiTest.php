<?php

namespace Tests\Feature\Api;

use App\Models\Catalog;
use App\Models\Color;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ColorTextureApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_colors_table_does_not_contain_a_hex_code_column(): void
    {
        $this->assertFalse(Schema::hasColumn('colors', 'hex_code'));
    }

    public function test_color_texture_is_streamed_with_webgl_compatible_cors_headers(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('colors/velvet.jpg', 'catalog-photo-bytes');

        $catalog = Catalog::create([
            'name' => 'Velvet',
            'slug' => 'velvet',
            'status' => 'published',
        ]);
        $color = Color::create([
            'catalog_id' => $catalog->id,
            'name' => 'Ocean',
            'code' => 'OCEAN',
            'sku' => 'OCEAN-1',
            'swatch_path' => 'colors/velvet.jpg',
        ]);

        $this->getJson("/api/v1/colors/{$color->id}")
            ->assertOk()
            ->assertJsonPath('data.texture_url', route('colors.texture', $color));

        $response = $this->get("/api/v1/colors/{$color->id}/texture");

        $response->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', '*')
            ->assertHeader('Cache-Control', 'max-age=86400, public');
        $this->assertSame('catalog-photo-bytes', $response->streamedContent());
    }

    public function test_color_texture_returns_not_found_without_an_uploaded_swatch(): void
    {
        Storage::fake('public');

        $catalog = Catalog::create([
            'name' => 'Velvet',
            'slug' => 'velvet',
            'status' => 'published',
        ]);
        $color = Color::create([
            'catalog_id' => $catalog->id,
            'name' => 'Ocean',
            'code' => 'OCEAN',
            'sku' => 'OCEAN-2',
        ]);

        $this->get("/api/v1/colors/{$color->id}/texture")->assertNotFound();
    }
}
