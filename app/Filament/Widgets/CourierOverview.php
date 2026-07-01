<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CourierOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();

        return [
            Stat::make('Assigned Shipments', Shipment::where('courier_id', $userId)->count()),
            Stat::make('Pending Pickup', Shipment::where('courier_id', $userId)->where('status', 'pending')->count()),
            Stat::make('In Transit', Shipment::where('courier_id', $userId)->where('status', 'in_transit')->count()),
            Stat::make('Delivered', Shipment::where('courier_id', $userId)->where('status', 'delivered')->count()),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('courier');
    }
}
