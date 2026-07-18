<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseAuth;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

class Login extends BaseAuth
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Username')
            ->required()
            ->autocomplete()
            ->autofocus();
    }
}
