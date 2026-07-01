<?php

namespace App\Filament\Resources\Shipments\Pages;

use App\Filament\Resources\Shipments\ShipmentResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard as ComponentsWizard;
use Filament\Schemas\Components\Wizard\Step as WizardStep;
use Illuminate\Support\HtmlString;

class ManageShipments extends ManageRecords
{
    protected static string $resource = ShipmentResource::class;

    protected ?string $paymentProof = null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('5xl')
                ->form([
                    ComponentsWizard::make([
                        WizardStep::make('Shipment & Package Details')
                            ->schema([
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
                                    ->afterStateUpdated(fn (Get $get, Set $set) => ShipmentResource::calculateFees($get, $set))
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
                                    ->afterStateUpdated(fn (Get $get, Set $set) => ShipmentResource::calculateFees($get, $set)),
                                TextInput::make('length')
                                    ->label('Length (cm)')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => ShipmentResource::calculateFees($get, $set)),
                                TextInput::make('width')
                                    ->label('Width (cm)')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => ShipmentResource::calculateFees($get, $set)),
                                TextInput::make('height')
                                    ->label('Height (cm)')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => ShipmentResource::calculateFees($get, $set)),
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
                        WizardStep::make('Payment & Upload Proof')
                            ->schema([
                                Placeholder::make('payment_instructions')
                                    ->label('Payment Instructions')
                                    ->content(fn (Get $get) => new HtmlString(
                                        ShipmentResource::getPaymentInstructionsHtml($get('total_shipping_fee') ?? 0)
                                    )),
                                FileUpload::make('payment_proof')
                                    ->label('Upload Payment Proof')
                                    ->image()
                                    ->directory('payment-proofs')
                                    ->visibility('public')
                                    ->required(),
                            ]),
                        WizardStep::make('Review & Submit')
                            ->schema([
                                Placeholder::make('confirmation_message')
                                    ->label('Confirmation')
                                    ->content('Thank you for completing your shipment request. Your shipment will be processed after payment verification and Admin approval.'),
                            ]),
                    ]),
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    if (! auth()->user()?->hasRole('super_admin')) {
                        $data['sender_id'] = auth()->id();
                    }

                    $this->paymentProof = $data['payment_proof'] ?? null;
                    unset($data['payment_proof']);

                    return $data;
                })
                ->after(function ($record) {
                    if ($this->paymentProof) {
                        $payment = $record->payment;
                        if ($payment) {
                            $payment->update([
                                'proof' => $this->paymentProof,
                            ]);
                        } else {
                            $record->payment()->create([
                                'amount' => $record->total_shipping_fee,
                                'proof' => $this->paymentProof,
                                'is_paid' => false,
                            ]);
                        }
                    }
                }),
        ];
    }
}
