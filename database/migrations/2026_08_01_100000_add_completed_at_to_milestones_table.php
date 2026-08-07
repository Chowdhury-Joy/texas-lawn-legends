<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The client dashboard timeline needs a real completion date per step. The
 * `updated_at` timestamp is not a substitute — any later edit to a milestone
 * (a typo fix in the description) would silently move its "completed" date.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status');
        });

        // Backfill existing completed milestones so the timeline is not blank
        // on rows that predate this column. updated_at is the closest signal
        // available for work that is already done.
        DB::table('milestones')
            ->where('status', 'completed')
            ->whereNull('completed_at')
            ->update(['completed_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
