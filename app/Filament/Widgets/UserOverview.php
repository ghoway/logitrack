<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = auth()->id();

        return [
            Stat::make('My Shipments', Shipment::where('sender_id', $userId)->count()),
            Stat::make('Pending', Shipment::where('sender_id', $userId)->where('status', 'pending')->count()),
            Stat::make('In Transit', Shipment::where('sender_id', $userId)->where('status', 'in_transit')->count()),
            Stat::make('Delivered', Shipment::where('sender_id', $userId)->where('status', 'delivered')->count()),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('user');
    }
}
