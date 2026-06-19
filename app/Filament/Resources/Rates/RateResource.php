<?php

namespace App\Filament\Resources\Rates;

use App\Filament\Resources\Rates\Pages\ManageRates;
use App\Models\Rate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RateResource extends Resource
{
    protected static ?string $model = Rate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';



    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('route_id')
                    ->relationship('route', 'id', fn ($query) => $query->where('is_active', true))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->origin} -> {$record->destination}")
                    ->searchable(['origin', 'destination'])
                    ->preload()
                    ->required(),
                Select::make('type')
                    ->options([
                        'pesawat' => 'Air / Pesawat',
                        'kapal' => 'Sea / Kapal',
                    ])
                    ->required(),
                TextInput::make('price_per_kg')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                TextInput::make('estimated_days')
                    ->numeric()
                    ->suffix('Days')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('route')
                    ->state(fn (Rate $record): string => "{$record->route->origin} -> {$record->route->destination}")
                    ->searchable(query: function ($query, string $search) {
                        $query->whereHas('route', function ($q) use ($search) {
                            $q->where('origin', 'like', "%{$search}%")
                              ->orWhere('destination', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pesawat' => 'info',
                        'kapal' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                TextColumn::make('price_per_kg')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('estimated_days')
                    ->suffix(' days')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ManageRates::route('/'),
        ];
    }
}
