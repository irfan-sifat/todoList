<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullName' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $hashPass = Hash::make($request->password);
        $insertData = [
            'name' => $request->fullName,
            'email' =>  $request->email,
            'password' => $hashPass
        ];

        User::create($insertData);
        return response()->json([
            'success' => true
        ]);
    }
}
