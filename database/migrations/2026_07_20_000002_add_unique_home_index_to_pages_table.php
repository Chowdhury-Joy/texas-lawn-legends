<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Only one page may be the homepage. Without this, concurrent first-hits
     * could each insert an is_home row and the site would resolve an
     * arbitrary one thereafter.
     *
     * SQLite and Postgres support partial indexes, so the constraint applies
     * only to is_home = 1 rows and leaves the many is_home = 0 pages alone.
     * MySQL has no partial index, so it is skipped there.
     */
    public function up(): void
    {
        $this->collapseDuplicateHomepages();

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('CREATE UNIQUE INDEX pages_single_home_unique ON pages (is_home) WHERE is_home = 1');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('DROP INDEX IF EXISTS pages_single_home_unique');
        }
    }

    /**
     * Demote any extra homepages, keeping the oldest, so the index can be built.
     */
    private function collapseDuplicateHomepages(): void
    {
        $keep = DB::table('pages')->where('is_home', true)->orderBy('id')->value('id');

        if ($keep === null) {
            return;
        }

        DB::table('pages')
            ->where('is_home', true)
            ->where('id', '!=', $keep)
            ->update(['is_home' => false]);
    }
};
