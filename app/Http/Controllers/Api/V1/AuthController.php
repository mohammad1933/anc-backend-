<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private JwtService $jwt) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([...$request->validated(), 'role' => 'customer']);

        return $this->tokenResponse($user, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->attempt($request, 'customer');
    }

    public function adminLogin(LoginRequest $request): JsonResponse
    {
        return $this->attempt($request, 'admin');
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()]);
    }

    private function attempt(LoginRequest $request, string $role): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();
        if (! $user || $user->role !== $role || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'The provided credentials are invalid.'], 422);
        }

        return $this->tokenResponse($user);
    }

    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'token' => $this->jwt->issue($user),
            'token_type' => 'Bearer',
            'expires_in' => (int) config('jwt.ttl'),
            'user' => $user,
        ], $status);
    }
}
