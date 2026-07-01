<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking');
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $shipment = Shipment::with(['sender', 'rate.route', 'payment'])
            ->where('tracking_number', $validated['tracking_number'])
            ->first();

        return view('tracking', compact('shipment'));
    }
}
