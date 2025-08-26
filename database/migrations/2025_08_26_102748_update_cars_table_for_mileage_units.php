<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Add new columns for mileage with units
            $table->decimal('mileage_value', 10, 2)->nullable()->after('year');
            $table->enum('mileage_unit', ['km', 'mi'])->default('km')->after('mileage_value');

            // Keep old mileage column for backward compatibility during migration
            $table->unsignedInteger('mileage')->nullable()->change();
        });

        // Migrate existing data
        DB::statement("UPDATE cars SET mileage_value = mileage, mileage_unit = 'km' WHERE mileage IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['mileage_value', 'mileage_unit']);
        });
    }
};
