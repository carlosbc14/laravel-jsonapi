<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data.attributes.name' => ['required', 'string', 'max:255'],
            'data.attributes.email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email'],
            'data.attributes.password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['data.attributes.name'],
            'email' => $validated['data.attributes.email'],
            'password' => Hash::make($validated['data.attributes.password']),
        ]);

        $token = $user->createToken('api-token');

        return response()->json([
            'data' => [
                'type' => 'tokens',
                'attributes' => [
                    'token' => $token->plainTextToken,
                ],
            ],
        ], 201);
    }
}
