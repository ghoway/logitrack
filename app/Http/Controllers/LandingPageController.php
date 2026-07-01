<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Shipment;
use App\Models\User;

class LandingPageController extends Controller
{
    public function index()
    {
        $stats = [
            'shipments' => Shipment::count(),
            'delivered' => Shipment::where('status', 'delivered')->count(),
            'routes' => Route::where('is_active', true)->count(),
            'customers' => User::role('user')->count(),
        ];

        return view('landing', compact('stats'));
    }
}
