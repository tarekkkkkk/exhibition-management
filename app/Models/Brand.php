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
        'user_id'
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function expos()
    {
        return $this->belongsToMany(Expo::class, 'brand_expo');
    }

    public function brandExpo()
    {
        return $this->hasMany(BrandExpo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
