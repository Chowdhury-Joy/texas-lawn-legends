<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;

class TopAccountWidget extends BaseAccountWidget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';
}
