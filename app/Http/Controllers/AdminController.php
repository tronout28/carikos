<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function Registerasadmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins',
            'password' => 'required|string|confirmed|min:6',
            'phone_number' => 'required|string',
        ]);

        $admin = new Admin([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone_number' => $request->phone_number,
        ]);

        $admin->save();

        return response()->json([
            'message' => 'Successfully created admin!',
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

        $admin = $request->admin();

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'admin' => $admin,
        ]);
    }

    public function forgotpassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone_number' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->where('phone_number', $request->phone_number)->first();

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found',
            ], 404);
        }

        if ($admin) {

            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
    }

    public function Logout(Request $request)
    {
        $request->admin()->token()->delete();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
