<?php

namespace App\Domains\Habits\Models;

use App\Domains\Habits\Enums\HabitFrequency;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'focus_area',
        'frequency',
        'target_per_period',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'focus_area' => HealthFocusArea::class,
        'frequency' => HabitFrequency::class,
        'is_active' => 'boolean',
        'target_per_period' => 'integer',
        'sort_order' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(HabitCheckIn::class);
    }
}
