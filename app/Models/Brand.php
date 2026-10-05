<?php

namespace App\Models;

use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'vehicle_type', 'logo', 'is_active'])]
class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsToMany<VehicleType, $this>
     */
    public function vehicleTypes(): BelongsToMany
    {
        return $this->belongsToMany(VehicleType::class, 'brand_vehicle_type')->withTimestamps();
    }

    /**
     * @return HasMany<VehicleModel, $this>
     */
    public function models(): HasMany
    {
        return $this->hasMany(VehicleModel::class);
    }

    /**
     * @return HasMany<Vehicle, $this>
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }
}
