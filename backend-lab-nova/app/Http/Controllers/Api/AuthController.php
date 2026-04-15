<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with(['userDetail', 'role'])->where('email', $request->email)->first();

        $modules = [];

        if ($user) {
            $modules = $user->getModulesWithInfo();
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'modules' => $modules,
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load(['userDetail', 'role']);
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'      => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'phone'     => 'sometimes|nullable|string|max:20',
        ]);

        $user->getConnection()->table('users')
            ->where('id', $user->id)
            ->update(array_filter($validated, fn($v) => $v !== null));

        // Actualizar detalle si se envían campos de userDetail
        $detailFields = $request->only(['gender', 'birthdate', 'address', 'addon_address', 'notes']);
        if (!empty($detailFields)) {
            $user->userDetail()->updateOrCreate(
                ['user_id' => $user->id],
                array_filter($detailFields, fn($v) => $v !== null)
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'data'    => $user->fresh(['userDetail', 'role']),
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta.',
            ], 422);
        }

        $user->getConnection()->table('users')
            ->where('id', $user->id)
            ->update(['password' => Hash::make($request->password)]);

        return response()->json(['success' => true, 'message' => 'Contraseña actualizada correctamente']);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error logging out: ' . $e->getMessage(),
            ], 500);
        }
    }
}
