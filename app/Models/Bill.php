<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $fillable=[
        'customer_name','customer_email','created_by','total_price','phone',
        'city','district','ward','description','payment_method','payment_type','status',
        'coupon_id','discount','notes','discount_loyalty'
    ];
    public function creator(){
        return $this->belongsTo(User::class,'created_by');
    }
    public function services(){
        return $this->belongsToMany(Service::class,'bill_services');
    }
    public function products(){
        return $this->belongsToMany(Product::class,'bill_products')->withPivot('quantity');
    }
    public function pets(){
        return $this->belongsToMany(Pet::class,'bill_pets')->withPivot('quantity');
    }
    public function coupon(){
        return $this->belongsTo(Coupon::class,'coupon_id');
    }
    public function hotels(){
        return $this->belongsToMany(Hotel::class,'bill_hotels');
    }
}
