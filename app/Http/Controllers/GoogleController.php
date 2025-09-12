<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => encrypt('123456dummy')
            ]
        );
        $token = $user->createToken('google-login')->plainTextToken;
        return redirect()->away("http://localhost:8080/login-success?token={$token}&role={$user->role}&id={$user->id}");
    } catch (Exception $e) {
        return response()->json(['message' => $e->getMessage()], 401);
    }
}
}
