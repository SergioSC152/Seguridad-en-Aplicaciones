<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoginProtectionService;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario (POST /api/register)
     */
    public function register(Request $request): JsonResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email')))]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\p{M}]+(?:[ \x{0027}\x{2019}\-][\p{L}\p{M}]+)*$/u'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email', Rule::notIn([mb_strtolower((string) config('cowapp.mail_settings_admin_email'))])],
            'password' => ['required', 'string', 'min:8', 'max:25'],
        ], [
            'name.regex' => 'El nombre solo admite letras, espacios, apóstrofos y guiones; no admite números.',
            'email.unique' => 'Ya existe un usuario con ese correo. Inicia sesión o recupera tu contraseña.',
            'email.not_in' => 'Este correo está reservado para la cuenta administradora.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Autenticación y generación de token (POST /api/login)
     */
    public function login(Request $request, LoginProtectionService $protection): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'max:25'],
        ]);
        $credentials['email'] = mb_strtolower(trim($credentials['email']));
        if ($protection->blocked($credentials['email'])) {
            return response()->json(['message' => 'Acceso bloqueado temporalmente.', 'retry_after' => $protection->seconds($credentials['email'])], 429);
        }

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            $protection->failed($credentials['email']);
            if ($protection->blocked($credentials['email'])) {
                return response()->json(['message' => 'Cinco intentos fallidos: acceso bloqueado temporalmente por correo.', 'retry_after' => $protection->seconds($credentials['email'])], 429);
            }
            return response()->json([
                'message' => $user ? 'Contraseña incorrecta.' : 'No existe un usuario con ese correo.',
            ], 401);
        }
        $protection->clear($credentials['email']);
        if ($user->mfa_enabled) return response()->json(['message'=>'Esta cuenta requiere MFA. Inicia sesión mediante el acceso web protegido.'],403);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Consulta del perfil autenticado (GET /api/profile)
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ], 200);
    }

    /**
     * Cierre de sesión e invalidación de token (POST /api/logout)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente. Token revocado.',
        ], 200);
    }
}
