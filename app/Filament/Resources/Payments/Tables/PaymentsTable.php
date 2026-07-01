<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shipment.tracking_number')
                    ->label('Tracking Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shipment.sender.name')
                    ->label('Sender')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                ImageColumn::make('proof')
                    ->label('Receipt')
                    ->width(50)
                    ->height(50)
                    ->placeholder('No Receipt')
                    ->square(),
                TextColumn::make('is_paid')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Paid' : 'Unpaid')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_paid')
                    ->label('Payment Status')
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Payment $record): bool => auth()->user()?->hasRole('super_admin') || (auth()->user()?->hasRole('user') && ! $record->is_paid)),
                Action::make('approvePayment')
                    ->label('Approve Payment')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Payment $record): bool => auth()->user()?->hasRole('super_admin') && ! $record->is_paid)
                    ->requiresConfirmation()
                    ->action(function (Payment $record) {
                        $record->update(['is_paid' => true]);

                        $shipment = $record->shipment;
                        if ($shipment && $shipment->status === 'pending') {
                            $shipment->update(['status' => 'picked_up']);
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => auth()->user()?->hasRole('super_admin')),
            ]);
    }
}
