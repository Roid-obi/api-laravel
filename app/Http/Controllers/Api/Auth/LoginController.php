<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request) {
        try {
            $credential = $request->only('email','password');
            
            if (empty($credential['email']) && empty($credential['password'])) {
                throw new \Exception('Email and Password fields are required.');
            }
            
            if (empty($credential['email'])) {
                throw new \Exception('Email field is required.');
            }
            
            if (empty($credential['password'])) {
                throw new \Exception('Password field is required.');
            }
            
            if(auth()->attempt($credential)) {
                $user = $request->user();
                $token = $user->createToken('api-token')->plainTextToken;
    
                return response()->json([
                    'message' => 'Berhasil Login',
                    'user' => $user,
                    'token' => $token,
                ]);
            } else {
                throw new \Exception("Invalid Credentials.");
                
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }
    

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Berhasil Logout.',
        ]);
    }
    

}
