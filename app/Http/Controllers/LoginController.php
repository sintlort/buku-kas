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
    
    public function logout(Request $request){
        //di Validasi atau tidak dulu apa yang mau masuk
        // selanjutnya cari user yang login saat ini dengan menggunakan Auth::user()
        //Auth::user() akan return data user yang login saat ini
        //Auth::id() akan return id user yang login saat ini;
        //selanjutnya token bisa didelete dengan cara Auth::user()->tokens()->delete();
        //Selanjutnya bisa response yang km mau
        //jangan lupa logout itu harus post ya
        $user = Auth::user();
        if ($user->tokens()->delete()) {
            return response()->json(['message' => 'success'], 200);
        }
        return response()->json(['message' => 'failed'], 400);
        
    }
    
    public function change_password(Request $request){
        //Jangan lupa untuk di cek old password nya dengan current password yang digunakan pada saat login, old password yang dimaksud yaitu password yang sebelum ganti password
        //Untuk ganti password, password harus di hash menggunakan fungsi Hash::make
        //Setelah semua dijalankan, bisa dilakukan penyimpanan dengan menggunakan fungsi save();
        //setelah nya dapat return response sesuai standar yang digunakan
        $user = User::find(Auth::id());
        if (!empty($user)) {
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json(['message' => 'success', 'data' => $user], 200);
            } else {
                return response()->json(['message' => 'not found', 'data' => ""], 404);
            }
        } else {
            return response()->json(['message' => 'failed', 'data' => ''], 400);
        }
    }
}
