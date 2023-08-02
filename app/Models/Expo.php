<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'info',
        'image'
    ];

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
