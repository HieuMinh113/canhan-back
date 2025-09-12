<?php

namespace App\Services;

use App\Models\UserProfile;

class LoyaltyService{
    public function calculateRank(UserProfile $profile){
        $ranks = config('loyalty.ranks');
        $currentRank='bronze';
        foreach($ranks as $rank =>$info ){
            if($profile->point >= $info['min_points']){
                $currentRank=$rank;
            }
        }
        return $currentRank;
    }
    public function calculateDiscount(string $rank): float
    {
        return config("loyalty.ranks.$rank.discount",0);
    }
    public function calculateEarnPoint(string $rank , int $billAmount){
        $rate = config("loyalty.ranks.$rank.point_rate",0,1);
        return floor($billAmount * $rate/1000);
    }
}
