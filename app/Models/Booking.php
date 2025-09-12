<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Booking extends Model
{
    use HasFactory;
    protected $fillable=[
        'name','type','owner','phone','date','time','doctor_id','email','handled','commission'
    ];
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
