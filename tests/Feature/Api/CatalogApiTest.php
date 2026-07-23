<?php

namespace Tests\Feature\Api;

use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function test_catalogs_can_be_created_listed_filtered_and_shown_with_relationships(): void
    {
        $category = Category::create(['name' => 'Upholstery', 'slug' => 'upholstery', 'status' => 'active']);

        $response = $this->postJson('/api/v1/catalogs', [
            'category_id' => $category->id,
            'name' => 'Royal Velvet',
            'slug' => 'royal-velvet',
            'sku' => 'RV-2024',
            'material' => 'Velvet',
            'composition' => '100% Cotton',
            'status' => 'published',
            'is_featured' => true,
            'is_new' => true,
        ]);

        $response->assertCreated()->assertJsonPath('data.slug', 'royal-velvet');
        $catalog = Catalog::where('slug', 'royal-velvet')->firstOrFail();

        $this->getJson('/api/v1/catalogs?status=published&featured=1')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $catalog->id);
        $this->getJson("/api/v1/catalogs/{$catalog->id}")
            ->assertOk()->assertJsonPath('data.category.slug', 'upholstery');
    }

    public function test_catalog_validation_rejects_duplicate_slugs(): void
    {
        Catalog::create(['name' => 'Existing', 'slug' => 'existing', 'status' => 'draft']);

        $this->postJson('/api/v1/catalogs', ['name' => 'Duplicate', 'slug' => 'existing', 'status' => 'draft'])
            ->assertUnprocessable()->assertJsonValidationErrors('slug');
    }
}
