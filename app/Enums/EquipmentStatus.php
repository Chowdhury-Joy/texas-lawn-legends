<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EquipmentStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Maintenance = 'maintenance';
    case Retired = 'retired';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Maintenance => 'In Maintenance',
            self::Retired => 'Retired',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::Maintenance => 'warning',
            self::Retired => 'gray',
        };
    }
}
