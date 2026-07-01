<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Shipments', Shipment::count()),
            Stat::make('Pending Payments', Payment::where('is_paid', false)->count()),
            Stat::make('Revenue', 'Rp '.number_format(Shipment::sum('total_shipping_fee'), 0, ',', '.')),
            Stat::make('Active Couriers', User::role('courier')->count()),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}
