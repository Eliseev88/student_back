<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRegisterRequest;
use App\Http\Requests\AuthLoginRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register (AuthRegisterRequest $request)
    {
        $credentials = $request->only('name', 'email', 'password');
        $existingUser = User::where('email', $credentials['email'])->first();

        if ($existingUser) {
            return response(['message' => "User is already registered"], 409);
        }
        $newUser = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);
            if ($newUser) {
                $token = $newUser->createToken('student')->plainTextToken;
                $existingUser = User::find($newUser->id);
                $existingUser->remember_token = $token;
                $existingUser->save();
                $response = [
                    'user' => $newUser,
                    'token' => $token,
                ];

                return response($response, 201);
            }
        return response(['message' => "Request error"], 500);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $user = User::find($request->user()->id);
        $user->remember_token = null;
        $user->save();
        response(['message' => "Logout done"], 201);
    }

    public function login(AuthLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response([
               'message' => 'Wrong email or password'
            ], 401);
        }
       
        $token = $user->createToken('student')->plainTextToken;
        $existingUser = User::find($user->id);
        $existingUser->remember_token = $token;
        $existingUser->save();
        
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }
}
