<?php

namespace App\Filament\Resources\Users\Concerns;

use App\Models\User;
use App\Support\AccessPermissions;

/**
 * Persists the User form's "Access" checkbox list into permission rows.
 *
 * Must live on the Create/Edit *page* classes — Filament only calls the
 * afterCreate/afterSave hooks on Livewire page components, never on the
 * Resource class.
 *
 * The checkbox list carries only checked keys, so we diff against the role's
 * default preset: a checked key the role already grants needs no row, a
 * checked key beyond the preset becomes an explicit grant, and an unchecked
 * key the role would have granted becomes an explicit revoke. Storing only
 * the deltas means a later change to a role preset still flows through to
 * users who never overrode that key.
 */
trait SyncsAccessPermissions
{
    protected function afterCreate(): void
    {
        $this->syncAccessPermissions();
    }

    protected function afterSave(): void
    {
        $this->syncAccessPermissions();
    }

    protected function syncAccessPermissions(): void
    {
        /** @var User $record */
        $record = $this->getRecord();

        // Admins are unconditional superusers, so overrides are meaningless.
        if ($record->isAdmin()) {
            $record->permissions()->delete();
            $record->unsetRelation('permissions');

            return;
        }

        $checked = (array) ($this->data['access_keys'] ?? []);
        $defaults = AccessPermissions::defaultsFor($record->role);

        $rows = [];

        foreach (array_keys(AccessPermissions::all()) as $key) {
            $isChecked = in_array($key, $checked, true);
            $isDefault = in_array($key, $defaults, true);

            if ($isChecked === $isDefault) {
                continue;
            }

            $rows[] = ['key' => $key, 'allowed' => $isChecked];
        }

        $record->permissions()->delete();

        if ($rows !== []) {
            $record->permissions()->createMany($rows);
        }

        // Drop the stale relation so canAccessKey() reads the new rows.
        $record->unsetRelation('permissions');
    }
}
