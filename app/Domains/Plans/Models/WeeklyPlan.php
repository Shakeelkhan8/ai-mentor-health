<?php

namespace App\Domains\Plans\Models;

use App\Domains\Plans\Enums\PlanStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyPlan extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'status',
        'title',
        'summary',
        'items',
        'generated_at',
    ];

    protected $casts = [
        'week_start' => 'date',
        'status' => PlanStatus::class,
        'items' => 'array',
        'generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
