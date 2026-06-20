<?php

namespace App\Filament\Resources\Shipments;

use App\Filament\Resources\Shipments\Pages\CreateShipment;
use App\Filament\Resources\Shipments\Pages\EditShipment;
use App\Filament\Resources\Shipments\Pages\ListShipments;
use App\Filament\Resources\Shipments\Schemas\ShipmentForm;
use App\Filament\Resources\Shipments\Tables\ShipmentsTable;
use App\Models\Bank;
use App\Models\Rate;
use App\Models\Shipment;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

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

    public static function calculateFees(Get $get, Set $set): void
    {
        $length = (float) ($get('length') ?? 0);
        $width = (float) ($get('width') ?? 0);
        $height = (float) ($get('height') ?? 0);
        $actualWeight = (float) ($get('actual_weight') ?? 0);

        $volumeWeight = ($length * $width * $height) / 6000;
        $chargeableWeight = max($actualWeight, $volumeWeight);
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

    public static function getPaymentInstructionsHtml(mixed $totalFee): string
    {
        $formattedFee = 'Rp '.number_format((float) $totalFee, 0, ',', '.');
        $banks = Bank::where('is_active', true)->get();

        if ($banks->isEmpty()) {
            return "Please transfer {$formattedFee} to Bank BCA 123456789 a/n LogiTrack.";
        }

        $html = "<div class='space-y-6'>";
        $html .= "<div class='text-lg font-semibold text-gray-800 dark:text-gray-200'>Total Payment Due: <span class='text-primary-600 dark:text-primary-400 font-bold'>{$formattedFee}</span></div>";
        $html .= "<div class='grid grid-cols-1 md:grid-cols-2 gap-6'>";

        foreach ($banks as $bank) {
            $logoHtml = '';
            if ($bank->bank_logo) {
                $logoUrl = asset('storage/'.$bank->bank_logo);
                $logoHtml = "<img src='{$logoUrl}' alt='{$bank->bank_name}' class='h-8 object-contain mb-2' />";
            }

            $qrisHtml = '';
            if ($bank->qris_image) {
                $qrisUrl = asset('storage/'.$bank->qris_image);
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
        $html .= '</div>';

        return $html;
    }

    public static function form(Schema $schema): Schema
    {
        if ($schema->getOperation() === 'create') {
            return $schema
                ->components([
                    Wizard::make([
                        Step::make('Shipment & Package Details')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('sender_id')
                                        ->relationship('sender', 'name')
                                        ->label('Sender / Client')
                                        ->default(auth()->id())
                                        ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                                        ->dehydrated()
                                        ->required(),
                                    Select::make('rate_id')
                                        ->relationship('rate', 'id')
                                        ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->route->origin} -> {$record->route->destination} ({$record->type} - Rp ".number_format($record->price_per_kg, 0, ',', '.').'/kg)')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set))
                                        ->required(),
                                ]),
                                Section::make('Receiver Details')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('receiver_name')
                                                ->required(),
                                            TextInput::make('receiver_phone')
                                                ->label('Receiver Phone')
                                                ->tel()
                                                ->required(),
                                            Textarea::make('receiver_address')
                                                ->label('Receiver Address')
                                                ->rows(3)
                                                ->required()
                                                ->columnSpanFull(),
                                        ]),
                                    ]),
                                Section::make('Package Specifications')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextInput::make('actual_weight')
                                                ->label('Actual Weight (kg)')
                                                ->numeric()
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                                            TextInput::make('length')
                                                ->label('Length (cm)')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                                            TextInput::make('width')
                                                ->label('Width (cm)')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                                            TextInput::make('height')
                                                ->label('Height (cm)')
                                                ->numeric()
                                                ->default(0)
                                                ->required()
                                                ->live()
                                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateFees($get, $set)),
                                        ]),
                                        Grid::make(2)->schema([
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
                                        ]),
                                    ]),
                            ]),
                        Step::make('Payment & Upload Proof')
                            ->schema([
                                Placeholder::make('payment_instructions')
                                    ->label('Payment Instructions')
                                    ->content(fn (Get $get) => new HtmlString(
                                        self::getPaymentInstructionsHtml($get('total_shipping_fee') ?? 0)
                                    )),
                                FileUpload::make('payment_proof')
                                    ->label('Upload Payment Proof')
                                    ->image()
                                    ->directory('payment-proofs')
                                    ->visibility('public')
                                    ->required(),
                            ]),
                        Step::make('Review & Submit')
                            ->schema([
                                Placeholder::make('confirmation_message')
                                    ->label('Confirmation')
                                    ->content('Thank you for completing your shipment request. Your shipment will be processed after payment verification and Admin approval.'),
                            ]),
                    ]),
                ]);
        }

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
                                ->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.')),
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
