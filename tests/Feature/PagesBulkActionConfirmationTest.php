<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\Pages\Tables\PagesTable;
use Filament\Tables\Table;
use Tests\TestCase;

/**
 * Unpublishing removes a page from the live site immediately with no way to
 * undo short of republishing, so — matching LeadsTable's markLost convention
 * — it must prompt for confirmation before a bulk selection can trigger it.
 * Publishing carries no equivalent risk, so it stays a single click.
 */
class PagesBulkActionConfirmationTest extends TestCase
{
    private function bulkAction(string $name)
    {
        $table = PagesTable::configure(Table::make(new ListPages));

        return collect($table->getFlatBulkActions())->first(fn ($action) => $action->getName() === $name);
    }

    public function test_unpublish_requires_confirmation(): void
    {
        $this->assertTrue($this->bulkAction('unpublish')->shouldOpenModal());
    }

    public function test_publish_does_not_require_confirmation(): void
    {
        $this->assertFalse($this->bulkAction('publish')->shouldOpenModal());
    }
}
