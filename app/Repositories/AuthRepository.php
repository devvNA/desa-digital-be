<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $data)
    {
        if (! Auth::attempt($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password tidak valid',
            ], 401);
        }

        /** @var User|null $user */
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
        ], 200);
    }

    public function logout()
    {
        /** @var User|null $user */
        $user = Auth::guard('sanctum')->user();

        /** @var PersonalAccessToken|null $accessToken */
        $accessToken = $user?->currentAccessToken();

        if (! $user || ! $accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $accessToken->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout success',
        ], 200);
    }

    public function me()
    {
        /** @var User|null $user */
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'You are not logged in',
            ], 401);
        }

        $user->load(['roles.permissions', 'headOfFamily', 'familyMembers']);

        $permissions = $user->roles->flatMap->permissions->pluck('name')->unique()->values();
        $role = optional($user->roles->first())->name;

        $profilePicture = $user->profile_picture
            ?? $user->headOfFamily?->profile_picture
            ?? $user->familyMembers?->profile_picture;

        return response()->json([
            'success' => true,
            'message' => 'User data',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => $permissions,
                'profile_picture' => $profilePicture ? asset('storage/' . $profilePicture) : null,
                'role' => $role,
            ],
        ], 200);
    }
}
