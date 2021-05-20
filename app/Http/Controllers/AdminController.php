<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|string|confirmed',
        ]);
        $admin = Admin::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'password' => bcrypt($fields['password'])
        ]);

        // $token = $user->createToken('myapptoken')->plainTextToken;
        $token = $admin->createToken('Personal Access Token', ['admin'])->accessToken;


        $response = [
            'admin' => $admin,
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
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        //check email
        $admin = Admin::where('username', $fields['username'])->first();
        
        //Check password
        if(!$admin || !Hash::check($fields['password'], $admin->password)) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }
        // $token = $user->createToken('myapptoken')->plainTextToken;
        $token = $admin->createToken('Personal Access Token', ['admin'])->accessToken;


        $response = [
            'admin' => $admin,
            'token' => $token
        ];

        return response($response, 201);
    }
}
