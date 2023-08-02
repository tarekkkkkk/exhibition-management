<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'brand_expo_id'
    ];

    public function brand()
    {
        return $this->brandExpo()->brand();
    }

    public function brandExpo()
    {
        return $this->belongsTo(BrandExpo::class);
    }
    // public function user()
    // {
    //     return $this->belongsTo(Brand::class);
    // }

    public function favorites()
    {
        return $this->hasMany(Favourite::class);
    }
}
