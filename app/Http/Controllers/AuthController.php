<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function RegisterasUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'phone_number' => 'required|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully created user!',
            'user' => $user,
        ], 201);
    }

    public function RegisterasOwner(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'phone_number' => 'required|string|unique:users',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
            'role' => 'owner',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully created owner!',
            'owner' => $user,
        ], 201);
    }

    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = request(['email', 'phone_number', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = $request->user();

        $token = $user->createToken('auth_token')->plainTextToken;



        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function forgotpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone_number' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        if ($user) {

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
    }


    public function Logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
