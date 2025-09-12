<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use App\Models\UserProfile;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
{
    $user = User::with('UserProfile') 
        ->where('id', Auth::id())
        ->first();
    if (!$user) {
        return response()->json(['error' => 'Không tìm thấy người dùng'], 404);
    }
    $profile = $user->UserProfile;
    $rank = $profile ? app(LoyaltyService::class)->calculateRank($profile) : null;
    $point = $profile ? $profile->point : 0;
    return response()->json([
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'phone' => $user->UserProfile->phone ?? null,
        'address' => $user->UserProfile->address ?? null,
        'birthday' => $user->UserProfile->birthday ?? null,
        'gender' => $user->UserProfile->gender ?? null,
        'avatar' => $user->UserProfile->avatar ?? null,
        'rank' => $rank,
        'point' => $point,
    ]);
}
    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('profile', $imageName, 'public');
            $fullUrl = URL::to('/') . '/storage/' . $path;
            return response()->json(['url' => $fullUrl], 200);
        }
        return response()->json(['error' => 'No image uploaded'], 400);
    }
    public function update(Request $request){
        $user = User::findOrFail(Auth::id());
        $validated = $request->validate([
            'phone'=>'nullable|string',
            'address'=>'nullable|string',
            'avatar'=>'nullable|string',
            'gender'=>'nullable|string',
            'birthday'=>'nullable|date'
        ]);

        $userProfile = $user->UserProfile;

        if (!$userProfile) {
            $userProfile = new UserProfile(['user_id' => $user->id]);
        }

        $userProfile->fill([
            'phone' => $validated['phone'] ?? $userProfile->phone,
            'address' => $validated['address'] ?? $userProfile->address,
            'avatar' => $validated['avatar'] ?? $userProfile->avatar,
            'gender' => $validated['gender'] ?? $userProfile->gender,
            'birthday' => $validated['birthday'] ?? $userProfile->birthday,
        ]);
        $userProfile->save();
        return response()->json($user, 200);
    }
    
}
