<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue (Last 30 Days)';

    protected function getData(): array
    {
        $days = collect(range(29, 0))->map(fn ($day) => Shipment::whereDate('created_at', Carbon::today()->subDays($day))->sum('total_shipping_fee'));

        $labels = collect(range(29, 0))->map(fn ($day) => Carbon::today()->subDays($day)->format('M d'));

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $days->toArray(),
                    'borderColor' => '#22c55e',
                    'fill' => false,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}
