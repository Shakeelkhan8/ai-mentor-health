<?php

namespace App\Domains\Habits\Models;

use App\Domains\Habits\Enums\CheckInStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitCheckIn extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'check_in_date',
        'status',
        'note',
        'mood_score',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'status' => CheckInStatus::class,
        'mood_score' => 'integer',
    ];

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
