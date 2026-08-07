<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum ProductPart: int implements HasLabel, HasDescription
{
    case Website = 1;
    case Booking = 2;
    case Ops = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::Website => 'Part 1 — Website + CMS',
            self::Booking => 'Part 2 — Website + CMS + Booking',
            self::Ops => 'Part 3 — Website + CMS + Booking + Ops',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Website => 'Marketing site and content editing only. No quote funnel, portal, or job ops.',
            self::Booking => 'Adds lead capture and booking / estimate flows on top of the site.',
            self::Ops => 'Full product: projects, invoices, crews, member portal, and operations tools.',
        };
    }

    public function atLeast(self $minimum): bool
    {
        return $this->value >= $minimum->value;
    }
}
