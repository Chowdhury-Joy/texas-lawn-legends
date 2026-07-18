<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('neighborhood')->nullable();
            $table->integer('estimated_sqft')->nullable();
            $table->string('service_type')->nullable();
            $table->decimal('calculated_estimate_low', 10, 2)->nullable();
            $table->decimal('calculated_estimate_high', 10, 2)->nullable();
            $table->string('step_reached')->default('started');
            $table->enum('status', ['partial', 'qualified', 'booked', 'lost'])->default('partial');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('external_booking_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
