<?php

namespace App\Models;

use Database\Factories\VehicleTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'icon', 'is_active'])]
class VehicleType extends Model
{
    /** @use HasFactory<VehicleTypeFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsToMany<Brand, $this>
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_vehicle_type')->withTimestamps();
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
