<?php

namespace App\Domains\Identity\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'focus_areas',
        'primary_goal',
        'timezone',
        'onboarding_completed_at',
        'daily_reminder_time',
        'preferences',
    ];

    protected $casts = [
        'focus_areas' => 'array',
        'preferences' => 'array',
        'onboarding_completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }
}
