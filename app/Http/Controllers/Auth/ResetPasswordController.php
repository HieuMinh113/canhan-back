<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Events\PasswordResetSuccess;
use App\Events\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Str;
class ResetPasswordController extends Controller
{
    public function reset(Request $request){
        $request->validate([
            'token'=>'required',
            'email'=>'required|email',
            'password'=>'required|confirmed|min:6'
        ]);
        $status = Password::reset(
            $request->only('token','email','password','password_confirmation'),
            function ($user) use ($request){
                $user->forceFill([
                    'password'=>Hash::make($request->password),
                    'remember_token'=>Str::random(60)
                ])->save();
            }
        );
        return $status === Password::PASSWORD_RESET
        ? response()->json(['message'=>'doi mat khau thanh cong'],200)
        : response()->json(['message'=>'token hoac email khong hop le'],400);
    }
}
