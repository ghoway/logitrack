<?php

namespace App\Filament\Resources\Shipments\Tables;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Shipment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sender.name')
                    ->label('Sender')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('courier.name')
                    ->label('Courier')
                    ->placeholder('Unassigned')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rate')
                    ->state(fn (Shipment $record): string => "{$record->rate->route->origin} -> {$record->rate->route->destination} ({$record->rate->type})")
                    ->label('Route / Type'),
                TextColumn::make('receiver_name')
                    ->label('Receiver')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('chargeable_weight')
                    ->label('Weight')
                    ->suffix(' kg')
                    ->sortable(),
                TextColumn::make('total_shipping_fee')
                    ->label('Fee')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'picked_up' => 'warning',
                        'in_transit' => 'info',
                        'delivered' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->sortable(),
                ImageColumn::make('delivery_proof')
                    ->label('Delivery Proof')
                    ->square()
                    ->placeholder('No proof')
                    ->visible(fn (): bool => auth()->user()?->hasRole('super_admin') || auth()->user()?->hasRole('user')),
                TextColumn::make('payment.is_paid')
                    ->label('Paid')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state): string => $state ? 'Paid' : 'Unpaid')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'picked_up' => 'Picked Up',
                        'in_transit' => 'In Transit',
                        'delivered' => 'Delivered',
                    ]),
                TernaryFilter::make('is_paid')
                    ->label('Payment Status')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('payment', fn ($q) => $q->where('is_paid', true)),
                        false: fn (Builder $query) => $query->whereHas('payment', fn ($q) => $q->where('is_paid', false)),
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Shipment $record): bool => auth()->user()?->hasRole('super_admin') || (auth()->user()?->hasRole('courier') && $record->courier_id === auth()->id())),
                Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('info')
                    ->visible(fn (Shipment $record): bool => auth()->user()?->hasRole('super_admin') || (auth()->user()?->hasRole('courier') && $record->courier_id === auth()->id()))
                    ->form([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'picked_up' => 'Picked Up',
                                'in_transit' => 'In Transit',
                                'delivered' => 'Delivered',
                            ])
                            ->live()
                            ->required(),
                        FileUpload::make('delivery_proof')
                            ->label('Delivery Proof Image')
                            ->image()
                            ->directory('delivery-proofs')
                            ->visibility('public')
                            ->visible(fn (Get $get): bool => $get('status') === 'delivered')
                            ->required(fn (Get $get): bool => $get('status') === 'delivered'),
                    ])
                    ->action(fn (Shipment $record, array $data) => $record->update($data)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => auth()->user()?->hasRole('super_admin')),
            ]);
    }
}
