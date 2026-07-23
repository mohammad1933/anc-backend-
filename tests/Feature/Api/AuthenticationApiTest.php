<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_customer_can_register_login_and_access_their_profile(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.role', 'customer');
        $user = User::where('email', 'jane@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secret123', $user->password));

        $token = $response->json('token');
        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'jane@example.com');
        $this->withToken($token)->getJson('/api/v1/dashboard')->assertForbidden();

        $this->postJson('/api/v1/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'secret123',
        ])->assertOk()->assertJsonStructure(['token', 'user']);
    }

    public function test_admin_login_protects_dashboard_and_management_mutations(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'm@m.com',
            'password' => 'mohamad1950',
            'role' => 'admin',
        ]);

        $this->getJson('/api/v1/dashboard')->assertUnauthorized();
        $this->postJson('/api/v1/catalogs', [
            'name' => 'Protected',
            'slug' => 'protected',
            'status' => 'draft',
        ])->assertUnauthorized();

        $login = $this->postJson('/api/v1/auth/admin/login', [
            'email' => 'm@m.com',
            'password' => 'mohamad1950',
        ])->assertOk()->assertJsonPath('user.role', 'admin');

        $this->withToken($login->json('token'))->getJson('/api/v1/dashboard')->assertOk();
        $this->withToken($login->json('token'))->postJson('/api/v1/catalogs', [
            'name' => 'Protected',
            'slug' => 'protected',
            'status' => 'draft',
            'is_featured' => false,
            'is_new' => false,
        ])->assertCreated();
    }

    public function test_admin_credentials_are_rejected_by_customer_login(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'm@m.com',
            'password' => 'mohamad1950',
            'role' => 'admin',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'm@m.com',
            'password' => 'mohamad1950',
        ])->assertUnprocessable();
    }
}
