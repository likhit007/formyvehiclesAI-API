<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types')->nullOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('vehicle_model_id')->nullable()->constrained('vehicle_models')->nullOnDelete();
            $table->string('registration_number');
            $table->string('vehicle_type')->nullable()->index();
            $table->string('model_name')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('gear_type')->nullable();
            $table->string('color')->nullable();
            $table->string('category')->nullable();
            $table->string('seating_capacity')->nullable();
            $table->decimal('mileage_km', 8, 2)->nullable();
            $table->boolean('is_taxi')->default(false);
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('registration_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
