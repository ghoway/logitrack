<?php

namespace App\Filament\Resources\Shipments;

use App\Filament\Resources\Shipments\Pages\ManageShipments;
use App\Models\Bank;
use App\Models\Rate;
use App\Models\Shipment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Shipments & Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tracking_number')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('sender_id')
                    ->relationship('sender', 'name')
                    ->label('Sender / Client')
                    ->default(auth()->id())
                    ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                    ->dehydrated()
                    ->required(),
                Select::make('courier_id')
                    ->relationship('courier', 'name', fn ($query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'courier')))
                    ->label('Courier')
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin'))
                    ->nullable(),
                Select::make('rate_id')
                    ->relationship('rate', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->route->origin} -> {$record->route->destination} ({$record->type} - Rp ".number_format($record->price_per_kg, 0, ',', '.').'/kg)')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set))
                    ->required(),
                TextInput::make('receiver_name')
                    ->required(),
                TextInput::make('receiver_phone')
                    ->label('Receiver Phone')
                    ->tel()
                    ->required(),
                Textarea::make('receiver_address')
                    ->label('Receiver Address')
                    ->rows(3)
                    ->required(),
                TextInput::make('actual_weight')
                    ->label('Actual Weight (kg)')
                    ->numeric()
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                TextInput::make('length')
                    ->label('Length (cm)')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                TextInput::make('width')
                    ->label('Width (cm)')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                TextInput::make('height')
                    ->label('Height (cm)')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                TextInput::make('chargeable_weight')
                    ->label('Chargeable Weight (kg)')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                TextInput::make('total_shipping_fee')
                    ->label('Total Shipping Fee')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'picked_up' => 'Picked Up',
                        'in_transit' => 'In Transit',
                        'delivered' => 'Delivered',
                    ])
                    ->default('pending')
                    ->live()
                    ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                    ->required(),
                FileUpload::make('delivery_proof')
                    ->label('Delivery Proof Receipt')
                    ->image()
                    ->directory('delivery-proofs')
                    ->visibility('public')
                    ->visible(fn (Get $get): bool => $get('status') === 'delivered')
                    ->required(fn (Get $get): bool => $get('status') === 'delivered')
                    ->disabled(fn (?Shipment $record): bool => ! auth()->user()?->hasRole('super_admin') && $record && filled($record->delivery_proof))
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_number')
                    ->searchable(),
                TextColumn::make('sender.name')
                    ->label('Sender')
                    ->searchable(),
                TextColumn::make('receiver_name')
                    ->searchable(),
                TextColumn::make('rate.route.origin')
                    ->label('Route')
                    ->formatStateUsing(fn ($record) => "{$record->rate->route->origin} -> {$record->rate->route->destination}"),
                TextColumn::make('chargeable_weight')
                    ->label('Weight (kg)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_shipping_fee')
                    ->label('Fee')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'picked_up' => 'info',
                        'in_transit' => 'primary',
                        'delivered' => 'success',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'picked_up' => 'Picked Up',
                        'in_transit' => 'In Transit',
                        'delivered' => 'Delivered',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalWidth('5xl')
                    ->visible(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->schema([
                        ComponentsSection::make('Shipment Info')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('tracking_number'),
                                TextEntry::make('sender.name')
                                    ->label('Sender'),
                                TextEntry::make('receiver_name')
                                    ->label('Receiver Name'),
                                TextEntry::make('receiver_phone')
                                    ->label('Receiver Phone'),
                                TextEntry::make('receiver_address')
                                    ->label('Receiver Address')
                                    ->columnSpanFull(),
                                TextEntry::make('rate.route.origin')
                                    ->label('Origin'),
                                TextEntry::make('rate.route.destination')
                                    ->label('Destination'),
                            ]),
                        ComponentsSection::make('Package & Payment')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('actual_weight')
                                    ->label('Actual Weight (kg)'),
                                TextEntry::make('chargeable_weight')
                                    ->label('Chargeable Weight (kg)'),
                                TextEntry::make('total_shipping_fee')
                                    ->label('Total Fee')
                                    ->money('IDR'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'picked_up' => 'info',
                                        'in_transit' => 'primary',
                                        'delivered' => 'success',
                                    }),
                                ImageEntry::make('delivery_proof')
                                    ->label('Delivery Proof Receipt')
                                    ->columnSpanFull()
                                    ->visible(fn ($record): bool => $record->status === 'delivered' && filled($record->delivery_proof)),
                            ]),
                    ]),
                EditAction::make()
                    ->modalWidth('5xl')
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin')),
                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageShipments::route('/'),
        ];
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

    public static function getPaymentInstructionsHtml(float $totalFee): string
    {
        $activeBanks = Bank::where('is_active', true)->get();

        if ($activeBanks->isEmpty()) {
            return '<p class="text-gray-500">No payment methods available at the moment. Please contact support.</p>';
        }

        $html = '<div class="space-y-4">';
        $html .= '<div class="text-sm text-gray-600 dark:text-gray-400">';
        $html .= '<p class="mb-2">Please transfer <strong>Rp '.number_format($totalFee, 0, ',', '.').'</strong> to one of the following bank accounts:</p>';
        $html .= '</div>';

        foreach ($activeBanks as $bank) {
            $logoUrl = $bank->bank_logo && Storage::disk('public')->exists($bank->bank_logo)
                ? Storage::url($bank->bank_logo)
                : null;

            $qrisUrl = $bank->qris_image && Storage::disk('public')->exists($bank->qris_image)
                ? Storage::url($bank->qris_image)
                : null;

            $logoHtml = $logoUrl
                ? "<img src='{$logoUrl}' alt='{$bank->bank_name}' class='h-8 object-contain mb-2' />"
                : '';

            $qrisHtml = '';
            if ($qrisUrl) {
                $qrisHtml = "
                    <div class='mt-3 p-2 bg-white rounded border border-gray-200 dark:border-gray-700 inline-block'>
                        <p class='text-xs text-gray-500 mb-1 font-semibold text-center'>Scan QRIS to Pay</p>
                        <img src='{$qrisUrl}' alt='QRIS Code' class='w-32 h-32 object-contain mx-auto' />
                    </div>
                ";
            }

            $html .= "
                <div class='p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm'>
                    {$logoHtml}
                    <div class='text-sm font-bold text-gray-700 dark:text-gray-300'>{$bank->bank_name}</div>
                    <div class='mt-1 text-sm text-gray-600 dark:text-gray-400'>
                        <span class='font-semibold'>Account No:</span> <code class='bg-gray-200 dark:bg-gray-900 px-1 py-0.5 rounded'>{$bank->bank_no}</code>
                    </div>
                    <div class='text-sm text-gray-600 dark:text-gray-400'>
                        <span class='font-semibold'>Account Holder:</span> {$bank->account_name}
                    </div>
                    {$qrisHtml}
                </div>
            ";
        }

        $html .= '</div>';

        return $html;
    }
}
