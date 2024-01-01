<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'username'    => 'required',
            'password'    => 'required',
            'device_name' => 'required',
        ]);
     
        $user = User::where('username', $request->username)->first();
     
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token =  $user->createToken($request->device_name)->plainTextToken;
     
        return response()->json([
            "data"=>[
                "token" => $token
            ]
            ]);
    }
}
