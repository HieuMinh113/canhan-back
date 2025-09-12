<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\CartItem;
use App\Notifications\CustomResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'github_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // public function pay(){
    //     return $this ->hasMany(Pay::class);
    // }
    public function cartItems()
    {
    return $this->hasMany(CartItem::class, 'user_id');
    }
    public function UserProfile(){
        return $this->hasOne(UserProfile::class,'user_id','id');
    }
    public function sendPasswordResetNotification($token)
    {
    $this->notify(new CustomResetPasswordNotification($token));
    }
    public function booking(){
        return $this->belongsTo(Booking::class,'doctor_id');
    }
    public function appointment(){
        return $this->belongsTo(Appointment::class,'staff_id');
    }
}
