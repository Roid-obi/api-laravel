<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8'
        ],[
            // name
            'name.required' => 'Nama harus di isi.',
            'name.string' => 'Nama harus berupa string.',
            'name.min' => 'Panjang Nama minimal 3 karakter.',
            'name.max' => 'Nama Tidak bisa lebih dari 255 karakter.',
            // email
            'email.required' => 'Email harus di isi.',
            'email.email' => 'Email harus berupa email.',
            'email.unique' => 'Email sudah dipakai.',
            'email.max' => 'Panjang Email Tidak bisa lebih dari 255 karakter.',
            // password
            'password.required' => 'Password Harus di isi.',
            'password.string' => 'Password Harus berupa string.',
            'password.min' => 'Password Harus 8 karakter atau lebih'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Register Successfully.',
            'user' => $user,
            'access_token' => $token,
        ]);
    }
}
