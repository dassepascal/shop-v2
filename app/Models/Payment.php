<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperPayment
 */
class Payment extends Model
{
    protected $fillable = [ 'payment_id', 'order_id', ];
}
