<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Information')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('shipment_id')
                                ->relationship('shipment', 'tracking_number')
                                ->label('Shipment / Tracking Number')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),
                            TextInput::make('amount')
                                ->label('Amount to Pay')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),
                            Toggle::make('is_paid')
                                ->label('Is Paid / Confirmed')
                                ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                                ->dehydrated()
                                ->required()
                                ->columnSpan(1),
                        ]),
                    ]),

                Section::make('Proof of Payment')
                    ->description('Please upload a screenshot or photo of your bank transfer receipt.')
                    ->schema([
                        FileUpload::make('proof')
                            ->label('Payment Receipt')
                            ->image()
                            ->directory('payment-proofs')
                            ->visibility('public')
                            ->required(fn (string $operation): bool => $operation === 'create' || ! auth()->user()?->hasRole('super_admin'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
