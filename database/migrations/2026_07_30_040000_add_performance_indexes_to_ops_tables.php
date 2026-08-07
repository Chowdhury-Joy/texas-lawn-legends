<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the columns the booking flow, stalled-lead escalation, and the
 * dashboard widgets filter and sort on. Volume is low today; these are cheap
 * insurance before real client traffic.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->index('status');
            $table->index('scheduled_at');
            $table->index('escalated_at');
            $table->index('updated_at');
            $table->index('created_at');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('started_at');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('issue_date');
            $table->index('updated_at');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['scheduled_at']);
            $table->dropIndex(['escalated_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['started_at']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['issue_date']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
