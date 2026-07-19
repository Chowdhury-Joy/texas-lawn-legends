<?php

namespace App\Events;

use App\Models\Addon;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddonOrdered
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, mixed>  $context  Preserved client/portal context
     */
    public function __construct(
        public Addon $addon,
        public array $context = [],
    ) {}
}
