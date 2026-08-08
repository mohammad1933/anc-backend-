<?php

namespace Tests\Feature\Api;

use App\Models\Catalog;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CatalogDiscountApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function test_admin_can_set_a_discount_and_public_api_returns_calculated_sale_price(): void
    {
        $response = $this->postJson('/api/v1/catalogs', [
            'name' => 'Sale Velvet', 'slug' => 'sale-velvet', 'status' => 'published',
            'price' => 200, 'currency' => 'AED', 'discount_percent' => 25,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.has_active_discount', true)
            ->assertJsonPath('data.sale_price', '150.00');

        $this->getJson('/api/v1/catalogs?status=published&discounted=1')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.discount_percent', 25);
    }

    public function test_discount_filter_excludes_expired_and_non_discounted_catalogs(): void
    {
        Catalog::create(['name' => 'Expired', 'slug' => 'expired', 'status' => 'published', 'price' => 100, 'discount_percent' => 20, 'discount_ends_at' => now()->subMinute()]);
        Catalog::create(['name' => 'Regular', 'slug' => 'regular', 'status' => 'published', 'price' => 100]);

        $this->getJson('/api/v1/catalogs?discounted=1')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_discount_requires_a_regular_price_and_valid_date_range(): void
    {
        $this->postJson('/api/v1/catalogs', [
            'name' => 'Invalid Sale', 'slug' => 'invalid-sale', 'status' => 'published', 'currency' => 'AED',
            'discount_percent' => 20, 'discount_starts_at' => '2026-09-10', 'discount_ends_at' => '2026-09-01',
        ])->assertUnprocessable()->assertJsonValidationErrors(['price', 'discount_ends_at']);
    }

    public function test_admin_can_manage_and_remove_a_collection_discount_from_the_dashboard_endpoint(): void
    {
        $catalog = Catalog::create(['name' => 'Dashboard Sale', 'slug' => 'dashboard-sale', 'status' => 'published']);

        $this->patchJson("/api/v1/admin/catalogs/{$catalog->id}/discount", [
            'enabled' => true,
            'price' => 320,
            'currency' => 'AED',
            'discount_percent' => 15,
        ])->assertOk()
            ->assertJsonPath('data.has_active_discount', true)
            ->assertJsonPath('data.sale_price', '272.00');

        $this->patchJson("/api/v1/admin/catalogs/{$catalog->id}/discount", ['enabled' => false])
            ->assertOk()
            ->assertJsonPath('data.has_active_discount', false)
            ->assertJsonPath('data.sale_price', null);

        $this->assertNull($catalog->refresh()->discount_percent);
        $this->assertSame('320.00', $catalog->price);
    }
}
