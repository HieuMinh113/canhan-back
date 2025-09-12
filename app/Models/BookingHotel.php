<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingHotel extends Model
{
    use HasFactory;
    protected $fillable=[
        'hotel_id','check_in','check_out','phone','total_price','check_in_time','check_out_time','email','name','handled'
    ];
    public function hotel(){
        return $this -> belongsTo(Hotel::class, 'hotel_id');
    }
}
