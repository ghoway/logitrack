<?php

namespace App\Filament\Resources\Shipments\Schemas;

use App\Models\Rate;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Sender & Courier Details')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('sender_id')
                                ->relationship('sender', 'name')
                                ->label('Sender / Client')
                                ->default(auth()->id())
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin'))
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),
                            Select::make('courier_id')
                                ->relationship('courier', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'courier')))
                                ->label('Courier')
                                ->searchable()
                                ->preload()
                                ->visible(fn (): bool => auth()->user()?->hasRole('super_admin'))
                                ->nullable()
                                ->columnSpan(1),
                        ]),
                    ]),

                Section::make('Receiver Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('receiver_name')
                                ->required()
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->columnSpan(1),
                            TextInput::make('receiver_phone')
                                ->label('Receiver Phone')
                                ->tel()
                                ->required()
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->columnSpan(1),
                            Textarea::make('receiver_address')
                                ->label('Receiver Address')
                                ->rows(3)
                                ->required()
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Shipping Route & Status')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('rate_id')
                                ->relationship('rate', 'id')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->route->origin} -> {$record->route->destination} ({$record->type} - Rp ".number_format($record->price_per_kg, 0, ',', '.').'/kg)')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set))
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->required()
                                ->columnSpan(1),
                            Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'picked_up' => 'Picked Up',
                                    'in_transit' => 'In Transit',
                                    'delivered' => 'Delivered',
                                ])
                                ->default('pending')
                                ->live()
                                ->disabled(fn (): bool => auth()->user()?->hasRole('user'))
                                ->required()
                                ->columnSpan(1),
                            FileUpload::make('delivery_proof')
                                ->label('Delivery Proof Receipt')
                                ->image()
                                ->directory('delivery-proofs')
                                ->visibility('public')
                                ->visible(fn (Get $get): bool => $get('status') === 'delivered')
                                ->required(fn (Get $get): bool => $get('status') === 'delivered')
                                ->disabled(fn (?Shipment $record): bool => ! auth()->user()?->hasRole('super_admin') && $record && filled($record->delivery_proof))
                                ->dehydrated()
                                ->columnSpan(1),
                        ]),
                    ]),

                Section::make('Package Specifications & Weight Logic')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('actual_weight')
                                ->label('Actual Weight (kg)')
                                ->numeric()
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set))
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->columnSpan(1),
                            TextInput::make('length')
                                ->label('Length (cm)')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set))
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->columnSpan(1),
                            TextInput::make('width')
                                ->label('Width (cm)')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set))
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->columnSpan(1),
                            TextInput::make('height')
                                ->label('Height (cm)')
                                ->numeric()
                                ->default(0)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set))
                                ->disabled(fn (string $operation): bool => ! auth()->user()?->hasRole('super_admin') && ($operation !== 'create' || ! auth()->user()?->hasRole('user')))
                                ->columnSpan(1),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('chargeable_weight')
                                ->label('Chargeable Weight (kg)')
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),
                            TextInput::make('total_shipping_fee')
                                ->label('Total Shipping Fee')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function calculateFees(Get $get, Set $set): void
    {
        $length = (float) ($get('length') ?? 0);
        $width = (float) ($get('width') ?? 0);
        $height = (float) ($get('height') ?? 0);
        $actualWeight = (float) ($get('actual_weight') ?? 0);

        $volumetricWeight = ($length * $width * $height) / 6000;
        $chargeableWeight = max($actualWeight, $volumetricWeight);
        $set('chargeable_weight', round($chargeableWeight, 2));

        $rateId = $get('rate_id');
        if ($rateId) {
            $rate = Rate::find($rateId);
            if ($rate) {
                $totalFee = $chargeableWeight * $rate->price_per_kg;
                $set('total_shipping_fee', round($totalFee, 2));
            } else {
                $set('total_shipping_fee', 0);
            }
        } else {
            $set('total_shipping_fee', 0);
        }
    }
}
