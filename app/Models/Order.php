<?php

namespace App\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperOrder
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping', 'tax', 'user_id', 'state_id', 'payment', 'reference', 'pick', 'total',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment_infos(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getPaymentTextAttribute($value): string
{
    $texts = [
    'carte' => 'Carte bancaire',
    'virement' => 'Virement',
    'cheque' => 'Chèque',
    'mandat' => 'Mandat administratif',
    ];

    return $texts[$this->payment];
}

public function getTotalOrderAttribute(): float
{
    return $this->total + $this->shipping;
}

public function getTvaAttribute(): float
{
    return $this->tax > 0 ? $this->total / (1 + $this->tax) * $this->tax : 0;
}

public function getHtAttribute(): float
{
    return $this->total / (1 + $this->tax);
}
}
