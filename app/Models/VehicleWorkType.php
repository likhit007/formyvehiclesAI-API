<?php

namespace App\Models;

use Database\Factories\VehicleWorkTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'icon', 'is_active'])]
class VehicleWorkType extends Model
{
    /** @use HasFactory<VehicleWorkTypeFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany<VehicleWork, $this>
     */
    public function works(): HasMany
    {
        return $this->hasMany(VehicleWork::class, 'type_id');
    }
}
