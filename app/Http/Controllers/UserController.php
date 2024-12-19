<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {

    public function updateprofile(Request $request) {
        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users',
            'phone_number' => 'nullable|string|unique:users',
        ]);
    }

    public function getprofile() {
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function whatsapp(){
        return response()->json([
            'status' => 'success',
            'wa_number' => 'https://wa.me/6281338287451'
        ]);
    }
}
