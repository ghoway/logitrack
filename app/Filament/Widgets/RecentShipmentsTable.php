<?php

namespace App\Filament\Widgets;

use App\Models\Shipment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentShipmentsTable extends TableWidget
{
    protected static ?string $heading = 'Recent Shipments';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Shipment::query()->latest()->limit(10))
            ->columns([
                TextColumn::make('tracking_number')
                    ->searchable(),
                TextColumn::make('sender.name')
                    ->label('Sender'),
                TextColumn::make('receiver_name')
                    ->label('Receiver'),
                TextColumn::make('total_shipping_fee')
                    ->label('Fee')
                    ->money('IDR'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'picked_up' => 'info',
                        'in_transit' => 'primary',
                        'delivered' => 'success',
                    }),
                TextColumn::make('created_at')
                    ->dateTime(),
            ]);
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin');
    }
}
