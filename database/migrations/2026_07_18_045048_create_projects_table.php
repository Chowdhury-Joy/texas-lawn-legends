<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('unique_dashboard_hash')->unique();
            $table->string('client_name');
            $table->string('project_title');
            $table->string('neighborhood');
            $table->decimal('contract_value', 10, 2);
            $table->enum('status', ['scheduled', 'active', 'completed'])->default('scheduled');
            $table->date('started_at');
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
