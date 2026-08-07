<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\HasPrimarySaveAndDangerDelete;
use App\Filament\Resources\Users\Concerns\SyncsAccessPermissions;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    /**
     * Aliased because `getDangerDeleteAction()` is overridden below. A trait
     * method is flattened into this class, so `parent::` would resolve to
     * EditRecord (which has no such method) rather than to the trait.
     */
    use HasPrimarySaveAndDangerDelete {
        getDangerDeleteAction as protected baseDangerDeleteAction;
    }

    use SyncsAccessPermissions;

    protected static string $resource = UserResource::class;

    /**
     * `access_keys` is not a column, so seed it from the user's effective
     * grants rather than relying on the field's default.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var User $record */
        $record = $this->getRecord();

        $data['access_keys'] = $record->effectiveKeys();

        return $data;
    }

    protected function getDangerDeleteAction(): DeleteAction
    {
        return $this->baseDangerDeleteAction()
            ->visible(fn () => $this->getRecord()->id !== auth()->id());
    }
}
