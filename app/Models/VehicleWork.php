<?php

namespace App\Models;

use App\Enums\VehicleWorkCycle;
use Database\Factories\VehicleWorkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'vehicle_id',
    'type_id',
    'title',
    'date',
    'cycle',
    'is_completed',
    'status',
    'notes',
])]
class VehicleWork extends Model
{
    /** @use HasFactory<VehicleWorkFactory> */
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'vehicle_id' => 'integer',
        'type_id' => 'integer',
        'date' => 'date',
        'cycle' => VehicleWorkCycle::class,
        'is_completed' => 'boolean',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Vehicle, $this>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return BelongsTo<VehicleWorkType, $this>
     */
    public function workType(): BelongsTo
    {
        return $this->belongsTo(VehicleWorkType::class, 'type_id');
    }

    /**
     * @return BelongsTo<VehicleWorkType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(VehicleWorkType::class, 'type_id');
    }
}
