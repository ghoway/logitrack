<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Widgets\ChartWidget;

class ShipmentStatusChart extends ChartWidget
{
    protected ?string $heading = 'Shipments by Status';

    protected function getData(): array
    {
        $statuses = ['pending', 'picked_up', 'in_transit', 'delivered'];
        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = Shipment::where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Shipments',
                    'data' => $counts,
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#8b5cf6', '#22c55e'],
                ],
            ],
            'labels' => ['Pending', 'Picked Up', 'In Transit', 'Delivered'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}
