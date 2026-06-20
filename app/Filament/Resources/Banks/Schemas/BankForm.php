<?php

namespace App\Filament\Resources\Banks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bank_name')
                    ->label('Bank Name')
                    ->placeholder('e.g., Bank BCA, Bank Mandiri')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('bank_no')
                    ->label('Bank Account Number')
                    ->placeholder('e.g., 1234567890')
                    ->columnSpanFull(),
                TextInput::make('account_name')
                    ->label('Account Holder Name')
                    ->placeholder('e.g., PT LogiTrack Indonesia')
                    ->columnSpanFull(),
                FileUpload::make('bank_logo')
                    ->label('Bank Logo')
                    ->image()
                    ->directory('banks')
                    ->visibility('public')
                    ->columnSpanFull(),
                FileUpload::make('qris_image')
                    ->label('QRIS Payment Code')
                    ->image()
                    ->directory('banks')
                    ->visibility('public')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}
