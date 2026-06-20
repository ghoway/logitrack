<?php

namespace App\Filament\Resources\Shipments\Pages;

use App\Filament\Resources\Shipments\ShipmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShipment extends CreateRecord
{
    protected static string $resource = ShipmentResource::class;

    protected ?string $paymentProof = null;

    public function getMaxContentWidth(): string
    {
        return '5xl';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->hasRole('super_admin')) {
            $data['sender_id'] = auth()->id();
        }

        // Capture payment proof and remove it from data to prevent Eloquent saving errors
        $this->paymentProof = $data['payment_proof'] ?? null;
        unset($data['payment_proof']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $shipment = $this->record;

        if ($this->paymentProof) {
            $payment = $shipment->payment;
            if ($payment) {
                $payment->update([
                    'proof' => $this->paymentProof,
                ]);
            } else {
                $shipment->payment()->create([
                    'amount' => $shipment->total_shipping_fee,
                    'proof' => $this->paymentProof,
                    'is_paid' => false,
                ]);
            }
        }
    }
}
