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

    protected function getDangerDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->color('danger')
            ->outlined();
    }
}
