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
        'image',
        'user_id',
        'address'
    ];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_expo');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function brandExpo()
    {
        return $this->hasMany(BrandExpo::class);
    }
}
