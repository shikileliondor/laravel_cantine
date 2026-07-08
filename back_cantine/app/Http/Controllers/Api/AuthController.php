<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PinLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Identifiants invalides.',
            ], 422);
        }

        $user = $request->user();
        $deviceName = $request->validated('device_name') ?? 'flutter-mobile';

        return response()->json([
            'message' => 'Connexion reussie.',
            'token' => $user->createToken($deviceName)->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function loginWithPin(PinLoginRequest $request): JsonResponse
    {
        $pinCode = $request->validated('pin_code');

        $user = User::query()
            ->whereNotNull('pin_code')
            ->get()
            ->first(fn (User $user): bool => Hash::check($pinCode, $user->pin_code));

        if (! $user) {
            return response()->json([
                'message' => 'Code PIN invalide.',
            ], 422);
        }

        $deviceName = $request->validated('device_name') ?? 'pin-terminal';

        return response()->json([
            'message' => 'Connexion par code PIN reussie.',
            'token' => $user->createToken($deviceName)->plainTextToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Deconnexion reussie.',
        ]);
    }
}
