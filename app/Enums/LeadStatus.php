<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    case Partial = 'partial';
    case Qualified = 'qualified';
    case Booked = 'booked';
    case Lost = 'lost';

    public function getLabel(): string
    {
        return match ($this) {
            self::Partial => 'Partial',
            self::Qualified => 'Qualified',
            self::Booked => 'Booked',
            self::Lost => 'Lost',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Partial => 'gray',
            self::Qualified => 'warning',
            self::Booked => 'success',
            self::Lost => 'danger',
        };
    }
}
