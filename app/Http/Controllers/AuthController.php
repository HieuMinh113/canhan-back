<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use App\Events\UserRegistered;
use App\Events\UserLoggedIn;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:6',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    event(new UserRegistered($user, $token));
    event(new UserLoggedIn($user, $token));

    return response()->json(['message' => 'Đăng ký thành công', 'token' => $token, 'user' => $user]);
}
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return response()->json(['message' => 'Sai thông tin'], 401);
    }
    if ($request->has('required_role') && $user->role !== $request->required_role) {
        return response()->json(['message' => 'Bạn không có quyền truy cập'], 403);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    event(new UserLoggedIn($user, $token));

    return response()->json([
        'message' => 'Đăng nhập thành công',
        'token' => $token,
        'user' => $user,
    ]);
}

    public function logout(Request $request)
{
    $user = $request->user();

    if ($user && $user->currentAccessToken()) {
        $user->currentAccessToken()->delete();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    return response()->json(['message' => 'Không xác định người dùng'], 401);
}

}
