<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, User $user)
    {
        $user = $user->create([
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        Role::firstOrCreate(['name' => 'user']);

        $user->assignRole('user');

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Регистрация успешна',
            'roles' => $user->getRoleNames(),
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'token' => $token,
            'user' => auth()->user(),
            'roles' => auth()->user()->getRoleNames(),
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'token' => auth()->refresh(),
        ]);
    }

    public function me()
    {
        $token = request()->bearerToken();

        return response()->json([
            'token' => $token,
            'user' => auth()->user(),
            'roles' => auth()->user()->getRoleNames(),
        ]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

}
