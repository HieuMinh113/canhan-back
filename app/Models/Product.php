<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'category', 'price', 'image','type','stock','tag'];
    protected $casts = [
        'price' => 'float',
        'stock'=>'float'
    ];
}
