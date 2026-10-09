<?php

namespace App\Models;

use Database\Factories\VehicleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'vehicle_type_id',
    'brand_id',
    'vehicle_model_id',
    'registration_number',
    'vehicle_type',
    'model_name',
    'year',
    'fuel_type',
    'gear_type',
    'color',
    'category',
    'seating_capacity',
    'mileage_km',
    'is_taxi',
    'image_url',
    'is_active',
])]
class Vehicle extends Model
{
    /** @use HasFactory<VehicleFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'vehicle_type_id' => 'integer',
        'brand_id' => 'integer',
        'vehicle_model_id' => 'integer',
        'year' => 'integer',
        'mileage_km' => 'float',
        'is_taxi' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<VehicleType, $this>
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class);
    }

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return BelongsTo<VehicleModel, $this>
     */
    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    /**
     * @return HasMany<VehicleWork, $this>
     */
    public function works(): HasMany
    {
        return $this->hasMany(VehicleWork::class);
    }
}
