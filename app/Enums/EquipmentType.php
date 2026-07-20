<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum EquipmentType: string implements HasLabel
{
    case Vehicle = 'vehicle';
    case Machinery = 'machinery';
    case Tool = 'tool';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Vehicle => 'Vehicle (Truck, Trailer)',
            self::Machinery => 'Heavy Machinery (Mower)',
            self::Tool => 'Hand Tool (Trimmer, Blower)',
        };
    }
}
