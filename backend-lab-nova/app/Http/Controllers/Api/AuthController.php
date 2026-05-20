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
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'El correo es obligatorio.',
            'email.email'       => 'El formato del correo no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $user = User::with(['userDetail', 'role'])->where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'El correo electrónico ingresado no está registrado.'], 401);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'La contraseña ingresada es incorrecta.'], 401);
        }

        if (! $user->status) {
            return response()->json(['message' => 'Tu cuenta se encuentra inhabilitada. Contacta al administrador.'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'token'   => $token,
            'user'    => $user,
            'modules' => $user->getModulesWithInfo(),
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load(['userDetail.gender', 'role']);
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:100',
            'last_name'    => 'sometimes|string|max:100',
            'phone'        => 'sometimes|nullable|string|max:20',
            'gender_id'    => 'nullable|exists:genders,id',
            'birthdate'    => 'nullable|date',
            'address'      => 'nullable|string|max:100',
            'addon_address'=> 'nullable|string|max:50',
            'notes'        => 'nullable|string',
        ]);

        $userFields   = array_intersect_key($validated, array_flip(['name', 'last_name', 'phone']));
        $detailFields = array_intersect_key($validated, array_flip(['gender_id', 'birthdate', 'address', 'addon_address', 'notes']));

        if (!empty($userFields)) {
            $user->getConnection()->table('users')
                ->where('id', $user->id)
                ->update(array_filter($userFields, fn($v) => $v !== null));
        }

        if (!empty($detailFields)) {
            $user->userDetail()->updateOrCreate(
                ['user_id' => $user->id],
                $detailFields
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'data'    => $user->fresh(['userDetail.gender', 'role']),
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
