<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password as RulesPassword;

class ResetPasswordController extends Controller
{
    public function token(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ],[
            'email.required' => 'Email harus di isi.',
            'email.email' => 'Email harus berupa valid email.'
        ]);
        $status = Password::sendResetLink($request->only('email'));
        if ($status == Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => __($status)
            ]);
        }
        throw ValidationException::withMessages([
            'email' => [trans($status)]
        ]);
    }
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ],[
            'token.required' => 'Token harus di isi.',
            'email.required' => 'Email harus di isi.',
            'email.email' => 'Email harus berupa valid email.',
            'password.required' => 'Password harus di isi.'
        ]);

        $status = Password::reset(
            $request->only('email','password','token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                $user->tokens()->delete();
                event(new PasswordReset($user));
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'sukses',
                'message' => 'Password Berhasil Di Update.'
            ]);
        }
        return response()->json([
            'status' => 'gagal',
            'message' => __($status)
        ],500);
    }
}