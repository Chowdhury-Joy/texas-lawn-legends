<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_codes', function (Blueprint $table) {
            $table->dropUnique(['code']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['unique_dashboard_hash']);
            $table->dropUnique(['referral_code']);
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->dropUnique(['unique_token']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
            $table->dropUnique(['unique_access_token']);
        });

        $driver = Schema::getConnection()->getDriverName();

        if (! in_array($driver, ['sqlite', 'pgsql'], true)) {
            return;
        }

        DB::statement('CREATE UNIQUE INDEX access_codes_code_workspace_unique ON access_codes (trial_workspace_id, code)');
        DB::statement('CREATE UNIQUE INDEX leads_uuid_workspace_unique ON leads (trial_workspace_id, uuid)');
        DB::statement('CREATE UNIQUE INDEX projects_dashboard_workspace_unique ON projects (trial_workspace_id, unique_dashboard_hash)');
        DB::statement('CREATE UNIQUE INDEX projects_referral_workspace_unique ON projects (trial_workspace_id, referral_code) WHERE referral_code IS NOT NULL');
        DB::statement('CREATE UNIQUE INDEX proposals_token_workspace_unique ON proposals (trial_workspace_id, unique_token)');
        DB::statement('CREATE UNIQUE INDEX invoices_number_workspace_unique ON invoices (trial_workspace_id, invoice_number)');
        DB::statement('CREATE UNIQUE INDEX invoices_token_workspace_unique ON invoices (trial_workspace_id, unique_access_token)');
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['sqlite', 'pgsql'], true)) {
            DB::statement('DROP INDEX IF EXISTS access_codes_code_workspace_unique');
            DB::statement('DROP INDEX IF EXISTS leads_uuid_workspace_unique');
            DB::statement('DROP INDEX IF EXISTS projects_dashboard_workspace_unique');
            DB::statement('DROP INDEX IF EXISTS projects_referral_workspace_unique');
            DB::statement('DROP INDEX IF EXISTS proposals_token_workspace_unique');
            DB::statement('DROP INDEX IF EXISTS invoices_number_workspace_unique');
            DB::statement('DROP INDEX IF EXISTS invoices_token_workspace_unique');
        }

        Schema::table('invoices', function (Blueprint $table) {
            $table->unique(['unique_access_token']);
            $table->unique(['invoice_number']);
        });

        Schema::table('proposals', function (Blueprint $table) {
            $table->unique(['unique_token']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->unique(['referral_code']);
            $table->unique(['unique_dashboard_hash']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->unique(['uuid']);
        });

        Schema::table('access_codes', function (Blueprint $table) {
            $table->unique(['code']);
        });
    }
};
