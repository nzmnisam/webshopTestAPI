<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'postanski_broj' => 'required'
        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'postanski_broj' => $fields['postanski_broj'],
        ]);

        // $token = $user->createToken('myapptoken')->plainTextToken;
        $token = $user->createToken('Personal Access Token', ['user'])->accessToken;


        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete(); //baca error ali radi??

        return [
            'message' => 'Logged out'
        ];
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        //check email
        $user = User::where('email', $fields['email'])->first();
        
        //Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }
        // $token = $user->createToken('myapptoken')->plainTextToken;
        $token = $user->createToken('Personal Access Token', ['user'])->accessToken;


        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
}
