<?php

namespace App\Listeners;

use App\Events\AddonOrdered;
use App\Services\OperationsNotifier;

class NotifyOperationsOfAddonOrder
{
    public function __construct(
        protected OperationsNotifier $notifier,
    ) {}

    public function handle(AddonOrdered $event): void
    {
        $this->notifier->dispatch('New add-on order request', [
            'addon' => $event->addon->title,
            'price' => $event->addon->base_price,
            'unit' => $event->addon->price_unit,
            'context' => $event->context,
        ]);
    }
}
