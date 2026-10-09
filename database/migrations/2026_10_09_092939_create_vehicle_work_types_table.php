<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_work_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('vehicle_work_types')->insert([
            ['id' => 1, 'name' => 'Alert', 'slug' => 'alert', 'icon' => 'notifications', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Repair', 'slug' => 'repair', 'icon' => 'handyman', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],

            ['id' => 3, 'name' => 'Inspection', 'slug' => 'inspection', 'icon' => 'fact_check', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Insurance Renewal', 'slug' => 'insurance-renewal', 'icon' => 'shield', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'PUC / Pollution Check', 'slug' => 'puc-pollution-check', 'icon' => 'eco', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Tire & Wheel Service', 'slug' => 'tire-wheel-service', 'icon' => 'tire_repair', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Battery Service', 'slug' => 'battery-service', 'icon' => 'battery_charging_full', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Other', 'slug' => 'other', 'icon' => 'more_horiz', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_work_types');
    }
};
