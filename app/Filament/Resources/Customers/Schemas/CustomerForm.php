<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female']),
                TextInput::make('avatar'),
                Select::make('provider')
                    ->options(['google' => 'Google', 'apple' => 'Apple']),
                TextInput::make('provider_id'),
                DateTimePicker::make('email_verified_at'),
            ]);
    }
}
