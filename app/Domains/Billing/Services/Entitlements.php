<?php

namespace App\Domains\Billing\Services;

use App\Models\User;

/**
 * What a user's plan allows. The single place that decides Free vs Pro limits;
 * other domains ask here instead of checking subscriptions themselves.
 */
class Entitlements
{
    /**
     * Daily AI coach message allowance, or null for unlimited.
     */
    public function dailyCoachMessages(User $user): ?int
    {
        if (! config('mentor.features.subscriptions')) {
            return null;
        }

        if ($user->activeSubscription() !== null) {
            return null;
        }

        return max(0, (int) config('mentor.limits.free_coach_messages_per_day'));
    }
}
