<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('CREATE UNIQUE INDEX pages_slug_workspace_unique ON pages (trial_workspace_id, slug) WHERE slug IS NOT NULL');
            DB::statement('CREATE UNIQUE INDEX services_slug_workspace_unique ON services (trial_workspace_id, slug)');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('DROP INDEX IF EXISTS pages_slug_workspace_unique');
            DB::statement('DROP INDEX IF EXISTS services_slug_workspace_unique');
        }

        Schema::table('services', function (Blueprint $table) {
            $table->unique(['slug']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->unique(['slug']);
        });
    }
};
