<?php

namespace App\Filament\Resources\Shipments;

use App\Filament\Resources\Shipments\Pages\CreateShipment;
use App\Filament\Resources\Shipments\Pages\EditShipment;
use App\Filament\Resources\Shipments\Pages\ListShipments;
use App\Filament\Resources\Shipments\Schemas\ShipmentForm;
use App\Filament\Resources\Shipments\Tables\ShipmentsTable;
use App\Models\Shipment;
use BackedEnum;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Shipments & Payments';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('courier')) {
            return $query->where('courier_id', $user->id);
        }

        if ($user->hasRole('user')) {
            return $query->where('sender_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return ShipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShipmentsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shipment Details')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('tracking_number')
                                ->label('Tracking Number')
                                ->weight('bold'),
                            TextEntry::make('sender.name')
                                ->label('Sender'),
                            TextEntry::make('courier.name')
                                ->label('Courier')
                                ->placeholder('Unassigned'),
                        ]),
                        Grid::make(3)->schema([
                            TextEntry::make('receiver_name')
                                ->label('Receiver Name'),
                            TextEntry::make('receiver_phone')
                                ->label('Receiver Phone'),
                            TextEntry::make('receiver_address')
                                ->label('Receiver Address')
                                ->columnSpan(2),
                        ]),
                    ]),
                Section::make('Package Info & Fees')
                    ->schema([
                        Grid::make(4)->schema([
                            TextEntry::make('chargeable_weight')
                                ->label('Chargeable Weight')
                                ->suffix(' kg'),
                            TextEntry::make('total_shipping_fee')
                                ->label('Total Shipping Fee')
                                ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'pending' => 'gray',
                                    'picked_up' => 'warning',
                                    'in_transit' => 'info',
                                    'delivered' => 'success',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                        ]),
                    ]),
                Section::make('Delivery Proof')
                    ->visible(fn ($record) => $record && $record->status === 'delivered')
                    ->schema([
                        ImageEntry::make('delivery_proof')
                            ->label('Receipt Photo')
                            ->width(200)
                            ->height(200)
                            ->placeholder('No delivery proof uploaded.')
                            ->square(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListShipments::route('/'),
            'create' => CreateShipment::route('/create'),
            'edit' => EditShipment::route('/{record}/edit'),
        ];
    }
}
