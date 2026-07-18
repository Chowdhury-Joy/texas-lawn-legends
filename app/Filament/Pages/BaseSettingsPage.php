<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

/**
 * Base class for CMS settings pages backed by the key/value `settings` table.
 *
 * @property-read Schema $form
 */
abstract class BaseSettingsPage extends Page
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected string $view = 'filament.pages.settings-form';

    /**
     * Map of setting key => storage type for every field this page manages.
     *
     * @return array<string, string>
     */
    abstract protected function settingsMap(): array;

    /**
     * The form components rendered on this page.
     *
     * @return array<int, mixed>
     */
    abstract protected function formComponents(): array;

    /**
     * Settings group these keys belong to (used when persisting new rows).
     */
    protected function settingsGroup(): string
    {
        return 'general';
    }

    public function mount(): void
    {
        $state = [];

        foreach (array_keys($this->settingsMap()) as $key) {
            $state[$key] = Setting::get($key);
        }

        $this->form->fill($state);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->formComponents())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($this->settingsMap() as $key => $type) {
            if (array_key_exists($key, $data)) {
                Setting::set($key, $data[$key], $type, $this->settingsGroup());
            }
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    /**
     * @return array<int, Action>
     */
    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }
}
