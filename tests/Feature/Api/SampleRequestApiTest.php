<?php

namespace Tests\Feature\Api;

use App\Models\SampleRequest;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SampleRequestApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function test_public_sample_request_is_created_with_items_and_can_be_approved(): void
    {
        $response = $this->postJson('/api/v1/sample-requests', [
            'company_name' => 'Studio ANC',
            'full_name' => 'John Doe',
            'country' => 'United Arab Emirates',
            'delivery_address' => 'Building 4, Al Majaz',
            'city' => 'Sharjah',
            'email' => 'john@example.com',
            'phone' => '+971500000000',
            'items' => [
                ['sample_name' => 'Velvet 8020'],
                ['sample_name' => 'Linen 11106'],
            ],
        ]);

        $response->assertCreated()->assertJsonCount(2, 'data.items');
        $sampleRequest = SampleRequest::firstOrFail();
        $this->assertStringStartsWith('REQ-', $sampleRequest->reference);
        $this->getJson("/api/v1/sample-requests/{$sampleRequest->id}")
            ->assertOk()->assertJsonPath('data.reference', $sampleRequest->reference);

        $this->patchJson("/api/v1/sample-requests/{$sampleRequest->id}/status", ['status' => 'approved'])
            ->assertOk()->assertJsonPath('data.status', 'approved');
        $this->assertNotNull($sampleRequest->refresh()->reviewed_at);
    }

    public function test_sample_request_rejects_more_than_three_items(): void
    {
        $payload = [
            'full_name' => 'John Doe', 'country' => 'UAE', 'delivery_address' => 'Sharjah',
            'city' => 'Sharjah', 'email' => 'john@example.com', 'phone' => '123',
            'items' => collect(range(1, 4))->map(fn (int $number) => ['sample_name' => "Sample {$number}"])->all(),
        ];

        $this->postJson('/api/v1/sample-requests', $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('items');
    }
}
