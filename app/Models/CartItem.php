<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;
class CartItem extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id','product_id','quantity','pet_id'
    ];  
    public function product()
{
    return $this->belongsTo(Product::class);
}
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
public function pet()
{
    return $this->belongsTo(Pet::class, 'pet_id');
}
}
