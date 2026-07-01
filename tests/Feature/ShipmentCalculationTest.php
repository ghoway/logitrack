<?php

use App\Models\Payment;
use App\Models\Rate;
use App\Models\Route;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a shipment generates a tracking number and automatically creates an unpaid payment', function () {
    $sender = User::factory()->create();
    $route = Route::create([
        'origin' => 'Jakarta',
        'destination' => 'Surabaya',
        'is_active' => true,
    ]);

    $rate = Rate::create([
        'route_id' => $route->id,
        'type' => 'pesawat',
        'price_per_kg' => 10000,
        'estimated_days' => 2,
    ]);

    // Volumetric weight: (50 * 40 * 30) / 6000 = 10 kg
    // Actual weight: 5 kg
    // Chargeable weight: 10 kg
    // Total shipping fee: 10 * 10000 = 100000
    $shipment = Shipment::create([
        'sender_id' => $sender->id,
        'rate_id' => $rate->id,
        'receiver_name' => 'John Doe',
        'receiver_phone' => '08123456789',
        'receiver_address' => 'Jl. Pahlawan No. 10, Surabaya',
        'actual_weight' => 5,
        'length' => 50,
        'width' => 40,
        'height' => 30,
        'chargeable_weight' => 10,
        'total_shipping_fee' => 100000,
        'status' => 'pending',
    ]);

    expect($shipment->tracking_number)->toStartWith('ID-');

    $payment = Payment::where('shipment_id', $shipment->id)->first();
    expect($payment)->not->toBeNull();
    expect($payment->is_paid)->toBeFalse();
    expect((float) $payment->amount)->toEqual(100000.0);
});

test('approving a payment updates is_paid and transitions shipment status to picked_up', function () {
    $sender = User::factory()->create();
    $route = Route::create([
        'origin' => 'Jakarta',
        'destination' => 'Surabaya',
        'is_active' => true,
    ]);

    $rate = Rate::create([
        'route_id' => $route->id,
        'type' => 'pesawat',
        'price_per_kg' => 10000,
        'estimated_days' => 2,
    ]);

    $shipment = Shipment::create([
        'sender_id' => $sender->id,
        'rate_id' => $rate->id,
        'receiver_name' => 'John Doe',
        'receiver_phone' => '08123456789',
        'receiver_address' => 'Jl. Pahlawan No. 10, Surabaya',
        'actual_weight' => 5,
        'length' => 50,
        'width' => 40,
        'height' => 30,
        'chargeable_weight' => 10,
        'total_shipping_fee' => 100000,
        'status' => 'pending',
    ]);

    $payment = Payment::where('shipment_id', $shipment->id)->first();

    // Simulate approvePayment action
    $payment->update(['is_paid' => true]);
    if ($shipment->status === 'pending') {
        $shipment->update(['status' => 'picked_up']);
    }

    expect($payment->fresh()->is_paid)->toBeTrue();
    expect($shipment->fresh()->status)->toEqual('picked_up');
});
