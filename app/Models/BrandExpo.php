<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandExpo extends Model
{
    use HasFactory;

    public $table = 'brand_expo';
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function expo()
    {
        return $this->belongsTo(Expo::class);
    }
}