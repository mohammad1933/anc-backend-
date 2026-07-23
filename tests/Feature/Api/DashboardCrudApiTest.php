<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardCrudApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function test_dashboard_can_create_and_update_inventory_and_customer_records(): void
    {
        $categoryId = $this->postJson('/api/v1/categories', [
            'name' => 'Upholstery', 'slug' => 'upholstery', 'status' => 'active', 'sort_order' => 0, 'tags' => [],
        ])->assertCreated()->json('data.id');

        $catalogId = $this->postJson('/api/v1/catalogs', [
            'category_id' => $categoryId, 'name' => 'Royal Velvet', 'slug' => 'royal-velvet',
            'sku' => 'RV-1', 'status' => 'published', 'is_featured' => true, 'is_new' => true,
        ])->assertCreated()->json('data.id');

        $colorId = $this->postJson('/api/v1/colors', [
            'catalog_id' => $catalogId, 'name' => 'Midnight', 'code' => '8020-01', 'sku' => 'RV-1-01',
            'type' => 'plain', 'currency' => 'AED', 'stock_quantity' => 4, 'stock_status' => 'in_stock', 'is_active' => true,
        ])->assertCreated()->json('data.id');

        $serviceId = $this->postJson('/api/v1/services', [
            'title' => 'Fabric Supply', 'slug' => 'fabric-supply', 'description' => 'Stocked fabric supply.',
            'tags' => ['Wholesale'], 'status' => 'visible', 'sort_order' => 0,
        ])->assertCreated()->json('data.id');

        $customerId = $this->postJson('/api/v1/customers', [
            'contact_name' => 'Jane Doe', 'email' => 'jane@example.com', 'tier' => 'standard', 'status' => 'active',
        ])->assertCreated()->json('data.id');

        $this->patchJson("/api/v1/colors/{$colorId}/toggle")->assertOk()->assertJsonPath('data.is_active', false);
        $this->getJson('/api/v1/dashboard')->assertOk()
            ->assertJsonPath('catalogs.total', 1)->assertJsonPath('customers.total', 1);
        $this->assertIsInt($serviceId);
        $this->assertIsInt($customerId);
    }

    public function test_settings_are_upserted_by_key(): void
    {
        $payload = ['key' => 'brand_name', 'value' => 'ANC', 'group' => 'general', 'is_public' => true];
        $this->postJson('/api/v1/settings', $payload)->assertCreated()->assertJsonPath('data.value', 'ANC');
        $this->postJson('/api/v1/settings', [...$payload, 'value' => 'ANC Najjar'])->assertOk()->assertJsonPath('data.value', 'ANC Najjar');
        $this->getJson('/api/v1/settings')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_inventory_images_can_be_uploaded_and_replaced(): void
    {
        Storage::fake('public');

        $categoryResponse = $this->post('/api/v1/categories', [
            'name' => 'Upholstery',
            'slug' => 'upholstery',
            'status' => 'active',
            'sort_order' => 0,
            'image' => UploadedFile::fake()->image('category.jpg'),
        ])->assertCreated();
        $categoryId = $categoryResponse->json('data.id');
        $categoryPath = 'categories/'.basename($categoryResponse->json('data.image_path'));
        Storage::disk('public')->assertExists($categoryPath);

        $catalogResponse = $this->post('/api/v1/catalogs', [
            'category_id' => $categoryId,
            'name' => 'Royal Velvet',
            'slug' => 'royal-velvet',
            'status' => 'published',
            'is_featured' => false,
            'is_new' => false,
            'thumbnail' => UploadedFile::fake()->image('catalog.webp'),
            'pdf' => UploadedFile::fake()->create('catalog.pdf', 100, 'application/pdf'),
        ])->assertCreated();
        $catalogId = $catalogResponse->json('data.id');
        $oldCatalogPath = 'catalogs/'.basename($catalogResponse->json('data.thumbnail_path'));
        Storage::disk('public')->assertExists($oldCatalogPath);
        Storage::disk('public')->assertExists('catalog-pdfs/'.basename($catalogResponse->json('data.pdf_path')));

        $updatedCatalogResponse = $this->post("/api/v1/catalogs/{$catalogId}", [
            '_method' => 'PUT',
            'category_id' => $categoryId,
            'name' => 'Royal Velvet',
            'slug' => 'royal-velvet',
            'status' => 'published',
            'is_featured' => false,
            'is_new' => false,
            'thumbnail' => UploadedFile::fake()->image('replacement.png'),
        ])->assertOk();
        Storage::disk('public')->assertMissing($oldCatalogPath);
        Storage::disk('public')->assertExists('catalogs/'.basename($updatedCatalogResponse->json('data.thumbnail_path')));

        $colorResponse = $this->post('/api/v1/colors', [
            'catalog_id' => $catalogId,
            'name' => 'Midnight',
            'code' => 'MID',
            'sku' => 'MID-1',
            'type' => 'plain',
            'currency' => 'AED',
            'stock_quantity' => 1,
            'stock_status' => 'in_stock',
            'is_active' => true,
            'swatch' => UploadedFile::fake()->image('swatch.jpg'),
        ])->assertCreated();
        Storage::disk('public')->assertExists('colors/'.basename($colorResponse->json('data.swatch_path')));

        $serviceResponse = $this->post('/api/v1/services', [
            'title' => 'Fabric Supply',
            'slug' => 'fabric-supply',
            'description' => 'Premium fabrics.',
            'status' => 'visible',
            'sort_order' => 0,
            'image' => UploadedFile::fake()->image('service.jpg'),
        ])->assertCreated();
        Storage::disk('public')->assertExists('services/'.basename($serviceResponse->json('data.image_path')));
    }

    public function test_inquiries_can_be_processed_and_service_counts_are_derived(): void
    {
        $serviceId = $this->postJson('/api/v1/services', [
            'title' => 'Consultation',
            'slug' => 'consultation',
            'description' => 'Design consultation.',
            'status' => 'visible',
            'sort_order' => 0,
        ])->assertCreated()->json('data.id');

        $inquiry = [
            'service_id' => $serviceId,
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Project fabrics',
            'message' => 'Please contact me.',
            'status' => 'new',
        ];
        $inquiryId = $this->postJson('/api/v1/inquiries', $inquiry)->assertCreated()->json('data.id');

        $this->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonPath('data.0.inquiries_count', 1);
        $this->putJson("/api/v1/inquiries/{$inquiryId}", [...$inquiry, 'status' => 'responded'])
            ->assertOk()
            ->assertJsonPath('data.status', 'responded');
        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('inquiries.total', 1)
            ->assertJsonPath('inquiries.new', 0);
    }
}
