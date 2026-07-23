<?php

namespace Tests;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function authenticateAsAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->withToken(app(JwtService::class)->issue($user));

        return $user;
    }
}
