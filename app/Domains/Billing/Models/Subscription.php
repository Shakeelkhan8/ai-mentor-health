<?php

namespace App\Domains\Billing\Models;

use App\Domains\Billing\Enums\SubscriptionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'stripe_customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'canceled_at',
        'meta',
    ];

    protected $casts = [
        'status' => SubscriptionStatus::class,
        'trial_ends_at' => 'datetime',
        'current_period_starts_at' => 'datetime',
        'current_period_ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            SubscriptionStatus::Active,
            SubscriptionStatus::Trialing,
        ], true);
    }
}
