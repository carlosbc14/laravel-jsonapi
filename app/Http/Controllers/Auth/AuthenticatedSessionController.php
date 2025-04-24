<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'data.attributes.email' => ['required', 'string', 'email'],
            'data.attributes.password' => ['required', 'string'],
        ]);

        $email = $credentials['data']['attributes']['email'];
        $password = $credentials['data']['attributes']['password'];

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            throw ValidationException::withMessages([
                'data.attributes.email' => [__('auth.failed')],
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('api-token');

        return response()->json([
            'data' => [
                'type' => 'tokens',
                'attributes' => [
                    'token' => $token->plainTextToken,
                ],
            ],
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
