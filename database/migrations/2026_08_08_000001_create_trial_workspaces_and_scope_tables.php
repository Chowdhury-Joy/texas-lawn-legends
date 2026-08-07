<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $scopedTables = [
        'users',
        'settings',
        'pages',
        'services',
        'addons',
        'testimonials',
        'leads',
        'projects',
        'milestones',
        'progress_photos',
        'crews',
        'equipment',
        'maintenance_logs',
        'time_entries',
        'proposals',
        'invoices',
        'invoice_items',
        'access_codes',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('trial_workspaces')) {
            Schema::create('trial_workspaces', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();
                $table->string('niche_id');
                $table->unsignedBigInteger('owner_user_id')->nullable();
                $table->timestamp('expires_at');
                $table->unsignedTinyInteger('product_part')->default(3);
                $table->boolean('demo_mode')->default(true);
                $table->timestamps();
            });
        }

        $this->dropPagesHomeIndex();

        foreach ($this->scopedTables as $tableName) {
            if (! Schema::hasColumn($tableName, 'trial_workspace_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('trial_workspace_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('trial_workspaces')
                        ->cascadeOnDelete();
                });
            }
        }

        $this->recreatePagesHomeIndex();

        if (Schema::hasTable('trial_workspaces')) {
            $foreignKeys = collect(DB::select("PRAGMA foreign_key_list('trial_workspaces')"))
                ->pluck('from')
                ->all();

            if (! in_array('owner_user_id', $foreignKeys, true)) {
                Schema::table('trial_workspaces', function (Blueprint $table) {
                    $table->foreign('owner_user_id')
                        ->references('id')
                        ->on('users')
                        ->nullOnDelete();
                });
            }
        }

        if ($this->settingsUsesLegacyUniqueKey()) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique(['key']);
                $table->unique(['trial_workspace_id', 'key']);
            });
        }
    }

    public function down(): void
    {
        if (! $this->settingsUsesLegacyUniqueKey()) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique(['trial_workspace_id', 'key']);
                $table->unique(['key']);
            });
        }

        if (Schema::hasTable('trial_workspaces')) {
            Schema::table('trial_workspaces', function (Blueprint $table) {
                if ($this->hasForeignKey('trial_workspaces', 'owner_user_id')) {
                    $table->dropForeign(['owner_user_id']);
                }
            });
        }

        $this->dropPagesHomeIndex();

        foreach (array_reverse($this->scopedTables) as $tableName) {
            if (Schema::hasColumn($tableName, 'trial_workspace_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('trial_workspace_id');
                });
            }
        }

        $this->recreateLegacyPagesHomeIndex();

        Schema::dropIfExists('trial_workspaces');
    }

    private function dropPagesHomeIndex(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('DROP INDEX IF EXISTS pages_single_home_unique');
        }
    }

    private function recreatePagesHomeIndex(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('CREATE UNIQUE INDEX pages_single_home_unique ON pages (trial_workspace_id, is_home) WHERE is_home = 1');
        }
    }

    private function recreateLegacyPagesHomeIndex(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('CREATE UNIQUE INDEX pages_single_home_unique ON pages (is_home) WHERE is_home = 1');
        }
    }

    private function settingsUsesLegacyUniqueKey(): bool
    {
        if (! Schema::hasTable('settings') || ! Schema::hasColumn('settings', 'trial_workspace_id')) {
            return false;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $indexes = collect(DB::select("PRAGMA index_list('settings')"))
                ->pluck('name')
                ->all();

            return in_array('settings_key_unique', $indexes, true);
        }

        return true;
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver !== 'sqlite') {
            return true;
        }

        return collect(DB::select("PRAGMA foreign_key_list('{$table}')"))
            ->contains(fn (object $row): bool => $row->from === $column);
    }
};
