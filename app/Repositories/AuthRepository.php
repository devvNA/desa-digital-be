<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $data)
    {
        if (! Auth::attempt($data)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login success',
            'token' => $token,
        ], 200);
    }

    public function logout()
    {
        /** @var User $user */
        $user = Auth::guard('sanctum')->user();

        if (! $user || ! $user->currentAccessToken()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout success',
        ], 200);
    }

    public function me()
    {
        /** @var User $user */
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'message' => 'You are not logged in',
            ], 401);
        }

        $user->load('roles.permissions');

        $permissions = $user->roles->flatMap->permissions->pluck('name')->unique()->values();
        $role = optional($user->roles->first())->name;

        return response()->json([
            'message' => 'User data',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => $permissions,
                'role' => $role,
            ],
        ]);
    }
}
