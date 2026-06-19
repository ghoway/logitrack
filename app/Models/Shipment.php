<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Override;
use Illuminate\Support\Str;

class Shipment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'sender_id',
        'courier_id',
        'rate_id',
        'receiver_name',
        'receiver_phone',
        'receiver_address',
        'actual_weight',
        'length',
        'width',
        'height',
        'chargeable_weight',
        'total_shipping_fee',
        'status',
        'delivery_proof',
    ];

    #[Override]
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shipment) {
            $shipment->tracking_number = 'ID-' . now()->format('Ymd') . strtoupper(Str::random(4));
        });

        static::created(function ($shipment) {
            $shipment->payment()->create([
                'amount' => $shipment->total_shipping_fee,
                'proof' => '',
                'is_paid' => false,
            ]);
        });
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
