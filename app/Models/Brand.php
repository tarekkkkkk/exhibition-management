<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'info',
        'image',
        'expo_id',
        'user_id'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function expo(){
        return $this->belongsTo(Expo::class);
    }


    public function user(){
        return $this->belongsTo(User::class);
    }
}
