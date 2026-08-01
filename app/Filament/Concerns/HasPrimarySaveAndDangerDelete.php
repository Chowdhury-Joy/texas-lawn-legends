<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

/**
 * Edit pages: Save is the header main CTA; Delete sits in the form footer
 * as an outlined danger action (not the page’s primary button).
 *
 * @mixin EditRecord
 */
trait HasPrimarySaveAndDangerDelete
{
    /**
     * @return array<Action | ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->submit(null)
                ->action('save'),
        ];
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCancelFormAction(),
            $this->getDangerDeleteAction(),
        ];
    }

    /**
     * The record must be bound explicitly. Filament only injects it when an
     * action is rendered through its `Actions` schema component; the custom
     * page views that loop `getFormActions()` and echo each action directly
     * bypass that, and `DeleteAction::setUp()` registers a `hidden()` closure
     * that type-hints a non-null Model — so an unbound action fatals on render.
     */
    protected function getDangerDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->record($this->getRecord())
            ->color('danger')
            ->outlined();
    }
}
