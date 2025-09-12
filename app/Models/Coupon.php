<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class Coupon extends Model
{
    use HasFactory;
    protected $fillable=[
        'code','percent','used','usage_limit','expires_at'
    ];
    public function isValid() : bool
    {
        Log::info('Check coupon', [
        'id' => $this->id,
        'code' => $this->code,
        'expires_at' => $this->expires_at,
        'usage_limit' => $this->usage_limit,
        'used' => $this->used,
        'now' => now(),
    ]);
        if($this->expires_at&& now() ->gt($this->expires_at)) return false;
        if($this->usage_limit && $this->used >= $this->usage_limit) return false;
        return true;
    }
    public function bills()
    {
        return $this->hasMany(Bill::class, 'coupon_id');
    }
}
