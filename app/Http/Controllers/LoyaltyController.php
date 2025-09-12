<?php

namespace App\Http\Controllers;

use App\Events\LoyaltyCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\LoyaltyService;
use App\Models\User;

class LoyaltyController extends Controller
{
    public function me(){
        $user = Auth::user();
        if (!$user) {
        return response()->json(['error' => 'Chưa đăng nhập'], 401);
        }
        $profile = $user->UserProfile;

        $loyalty = app(LoyaltyService::class);
        $rank = $loyalty->calculateRank($profile);
        return response()->json([
            'point'=>$profile->point,
            'rank' => $rank,
            'discount'=>$loyalty->calculateDiscount($rank),
        ]);
    }
    public function index(LoyaltyService $loyalty)
{
    $authUser = Auth::user();
    if (!$authUser) {
        return response()->json(['error' => 'Chưa đăng nhập'], 401);
    }
    $users = User::with('userProfile')->where('role', 'user')->get();

    $data = $users->map(function ($u) use ($loyalty) {
        $profile = $u->userProfile;
        if (!$profile) {
            $profile = $u->userProfile()->create([
                'point' => 0
            ]);
        }
        $rank = $loyalty->calculateRank($profile);
        $point = $profile->point;
        $discount = $loyalty->calculateDiscount($rank);

        return [
            'id'       => $u->id,
            'name'     => $u->name,
            'email'    => $u->email,
            'point'    => $point,
            'rank'     => $rank,
            'discount' => $discount,
        ];
    });

    return response()->json($data);
}
    public function getemail(Request $request , LoyaltyService $loyalty){
        $email = $request -> query('email');
        if(!$email) return response()->json(['message'=>'email trong'],400);
        $user = User::where('email',$email)->first();
        if(!$user && !$user->UserProfile){
            return response()->json([
                'point'=>0,
                'rank'=>null,
                'discount'=>0,
            ]);
        }
        $profile = $user->UserProfile;
        $rank = $loyalty->calculateRank($profile);
        $discount = $loyalty->calculateDiscount($rank);
        return response()->json([
            'point' => $profile->point,
            'rank' => $rank,
            'discount' => $discount,
        ]);
    }

}
