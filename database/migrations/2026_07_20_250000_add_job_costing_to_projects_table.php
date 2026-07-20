<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('material_cost', 10, 2)->default(0)->after('contract_value');
            $table->decimal('labor_cost', 10, 2)->default(0)->after('material_cost');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['material_cost', 'labor_cost']);
        });
    }
};
