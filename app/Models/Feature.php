<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feature extends Model
{

    use HasFactory;
    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'feature_product')
                    ->withPivot('value')
                    ->withTimestamps();
    }
}
