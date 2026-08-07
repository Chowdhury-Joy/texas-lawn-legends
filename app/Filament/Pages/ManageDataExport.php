<?php

namespace App\Filament\Pages;

use App\Enums\LicenseTrack;
use App\Models\User;
use App\Services\DataExportService;
use App\Support\ProductFeatures;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * X-01 — Admin → Data Export.
 *
 * The page itself is visible on both licence tracks: Track A admins get the
 * download button, Track B admins get an honest explanation of how to get
 * their data (and what buying out unlocks) instead of a menu item that
 * silently isn't there. The export itself is guarded server-side in
 * DataExportService, so hiding the button is presentation, not security.
 */
class ManageDataExport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected static string|\UnitEnum|null $navigationGroup = 'Site Settings';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Data Export';

    protected static ?string $title = 'Full Data Export';

    protected string $view = 'filament.pages.manage-data-export';

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();
        $key = static::permissionKey();

        return ($user?->canAccessKey($key) ?? false) && ProductFeatures::allows($key);
    }

    public static function permissionKey(): string
    {
        return 'settings.data_export';
    }

    public function selfServeAllowed(): bool
    {
        return DataExportService::selfServeAllowed();
    }

    public function track(): LicenseTrack
    {
        return LicenseTrack::current();
    }

    /**
     * @return array{tables: array<string, int>, uploads: array{count: int, bytes: int}}
     */
    public function summary(): array
    {
        return app(DataExportService::class)->summary();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function tableGroups(): array
    {
        return DataExportService::TABLE_GROUPS;
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Download export (.zip)')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->visible(fn (): bool => $this->selfServeAllowed())
                ->requiresConfirmation()
                ->modalHeading('Download a full copy of your data?')
                ->modalDescription('Builds one ZIP with every business record as a spreadsheet-ready CSV plus all uploaded files. Large sites can take a moment. The file holds customer contact and billing details — keep it somewhere private.')
                ->modalSubmitActionLabel('Build & download')
                ->action(function (DataExportService $export): BinaryFileResponse {
                    $path = $export->generate(auth()->user());

                    return response()
                        ->download($path, basename($path))
                        ->deleteFileAfterSend();
                }),
        ];
    }
}
