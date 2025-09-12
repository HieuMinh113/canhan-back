<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Hotel extends Model
{
    use HasFactory;
    protected $fillable=[
        'name','category','price','description','image',
    ];
    protected $casts = [
        'price' => 'float',
    ];
    public function bookinghotel(){
        return $this ->hasMany(BookingHotel::class);
    }
}
