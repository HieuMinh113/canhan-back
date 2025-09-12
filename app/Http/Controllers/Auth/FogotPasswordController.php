<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
class FogotPasswordController extends Controller
{
    public function fogot(Request $request){
        $request->validate([
            'email'=>'required|email',
        ]);
        $status = Password::sendResetLink( $request->only('email'));
        return $status === Password::RESET_LINK_SENT
        ? response()->json(['message'=>'dat lai mat khau'],200)
        : response()->json(['message'=>'gui tha bai'],400);
    }
}
