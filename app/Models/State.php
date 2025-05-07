<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperState
 */
class State extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'indice',
    ];

    public $timestamps = false;

    public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}
}
