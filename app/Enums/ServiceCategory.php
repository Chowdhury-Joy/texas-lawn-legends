<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceCategory: string implements HasLabel
{
    case Create = 'create';
    case Care = 'care';

    public function getLabel(): string
    {
        return match ($this) {
            self::Create => 'The Create Suite',
            self::Care => 'The Care Suite',
        };
    }
}
