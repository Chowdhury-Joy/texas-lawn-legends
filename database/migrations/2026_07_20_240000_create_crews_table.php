<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crews', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('leader_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('color')->default('emerald');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('crew_id')->nullable()->after('lead_id')->constrained('crews')->nullOnDelete();
            $table->index('crew_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['crew_id']);
            $table->dropColumn('crew_id');
        });

        Schema::dropIfExists('crews');
    }
};
