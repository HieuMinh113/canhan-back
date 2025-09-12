<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Appointment extends Model
{
    use HasFactory;
    protected $fillable=[
        'name','date','time','service_id','staff_id','email','owner','handled','commission'
    ];
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
    public function service(){
        return $this->belongsTo(Service::class,'service_id');
    }
}
