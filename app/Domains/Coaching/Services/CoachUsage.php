<?php

namespace App\Domains\Coaching\Services;

use App\Domains\Billing\Services\Entitlements;
use App\Domains\Coaching\Enums\MessageRole;
use App\Domains\Coaching\Models\CoachMessage;
use App\Models\User;

/**
 * Counts a user's coach messages against their plan's daily allowance.
 *
 * "Today" starts at midnight in the user's profile timezone. Only the user's own
 * messages count, not the coach's replies. The count uses the
 * (user_id, created_at) index on coach_messages.
 *
 * The check is not atomic with the send: two requests racing at the last allowed
 * message can both pass. That overshoot is bounded by the `coach` rate limiter and
 * acceptable for a soft quota.
 */
class CoachUsage
{
    public function __construct(
        private readonly Entitlements $entitlements,
    ) {
    }

    public function dailyLimit(User $user): ?int
    {
        return $this->entitlements->dailyCoachMessages($user);
    }

    /**
     * Messages the user can still send today, or null when unlimited.
     */
    public function remainingToday(User $user): ?int
    {
        $limit = $this->dailyLimit($user);

        return $limit === null ? null : max(0, $limit - $this->sentToday($user));
    }

    public function sentToday(User $user): int
    {
        $timezone = $user->profile?->timezone ?: config('app.timezone', 'UTC');
        $startOfDay = now($timezone)->startOfDay()->setTimezone(config('app.timezone', 'UTC'));

        return CoachMessage::query()
            ->where('user_id', $user->id)
            ->where('role', MessageRole::User->value)
            ->where('created_at', '>=', $startOfDay)
            ->count();
    }
}
