<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }

        $token = $user->createToken('auth_token_' . $user->id)->plainTextToken;

        $org        = $user->organization;
        $planConfig = $org ? config("plans.{$org->plan}", []) : [];

        return response()->json([
            'message'      => 'Login successful',
            'access_token' => $token,
            'user'         => $this->formatUser($user),
            'organization' => $org ? [
                'id'             => $org->id,
                'name'           => $org->name,
                'plan'           => $org->plan,
                'business_model' => $org->business_model,
            ] : null,
            'features' => $org ? [
                'modules'  => $planConfig['modules'] ?? [],
                'channels' => $planConfig['channels'] ?? [],
            ] : null,
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Session closed successfully'], 200);
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    public function index(): JsonResponse
    {
        $users = User::with('roles')->get()->map($this->formatUser(...));
        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($this->formatUser($user->load('roles')));
    }

    public function store(Request $request): JsonResponse
    {
        $org = $request->user()->organization;

        if ($org) {
            $maxUsers = config("plans.{$org->plan}.max_users");
            if ($maxUsers !== null) {
                $currentCount = User::count();
                if ($currentCount >= $maxUsers) {
                    return response()->json([
                        'error'     => 'user_limit_reached',
                        'message'   => "Your plan allows a maximum of {$maxUsers} users.",
                        'max_users' => $maxUsers,
                    ], 403);
                }
            }
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'nullable|string|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return response()->json($this->formatUser($user), 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role'     => 'nullable|string|exists:roles,name',
        ]);

        $role = array_key_exists('role', $validated) ? $validated['role'] : false;
        unset($validated['role']);

        $user->update($validated);

        // false = campo no enviado (no tocar roles); null = envío explícito para limpiar
        if ($role !== false) {
            $user->syncRoles($role ? [$role] : []);
        }

        return response()->json($this->formatUser($user->fresh('roles')));
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['error' => 'You cannot delete your own account.'], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(null, 204);
    }

    private function formatUser(User $user): array
    {
        return [
            'id'              => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'organization_id' => $user->organization_id,
            'roles'           => $user->getRoleNames(),
        ];
    }
}
