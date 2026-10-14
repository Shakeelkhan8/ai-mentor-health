<?php

namespace App\Domains\Coaching\Actions;

use App\Domains\Coaching\Enums\MessageRole;
use App\Domains\Coaching\Models\CoachConversation;
use App\Domains\Coaching\Models\CoachMessage;
use App\Domains\Coaching\Services\CoachingService;
use App\Domains\Coaching\Services\CoachUsage;
use App\Domains\Shared\Actions\Action;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendCoachMessage extends Action
{
    private const HISTORY_LIMIT = 20;

    public function __construct(
        private readonly CoachingService $coachingService,
        private readonly CoachUsage $usage,
    ) {
    }

    /**
     * @return array{conversation_id: int, message: string, remaining_today: int|null}
     */
    public function execute(mixed ...$args): mixed
    {
        /** @var User $user */
        $user = $args[0];
        $content = trim((string) $args[1]);
        $conversationId = $args[2] ?? null;

        if ($content === '') {
            throw new DomainException('Message cannot be empty.', 'empty_message');
        }

        // Checked before any write or AI call, so a blocked message costs nothing.
        $remaining = $this->usage->remainingToday($user);
        if ($remaining === 0) {
            $limit = $this->usage->dailyLimit($user);

            throw new DomainException(
                "You've used your {$limit} free coach messages for today. Upgrade to Pro for unlimited coaching, or come back tomorrow.",
                'coach_daily_limit_reached',
            );
        }

        $conversation = $this->resolveConversation($user, $conversationId);

        $history = $conversation->messages()
            ->orderByDesc('id')
            ->limit(self::HISTORY_LIMIT)
            ->get()
            ->sortBy('id')
            ->values()
            ->map(fn (CoachMessage $message) => [
                'role' => $message->role->value,
                'content' => $message->content,
            ])
            ->all();

        // The AI call can take up to ~30 s, so it runs with no transaction open. Nothing
        // is saved until it succeeds: a failed reply leaves no half-conversation behind
        // and uses none of the user's daily allowance.
        try {
            $reply = $this->coachingService->reply($content, $history);
        } catch (DomainException $e) {
            Log::error('AI coach reply failed', [
                'user_id' => $user->id,
                'conversation_id' => $conversation->id,
                'code' => $e->errorCode,
                'reason' => $e->getPrevious()?->getMessage() ?? $e->getMessage(),
            ]);

            throw $e;
        }

        return DB::transaction(function () use ($user, $content, $conversation, $reply, $remaining) {
            $conversation->messages()->create([
                'user_id' => $user->id,
                'role' => MessageRole::User,
                'content' => $content,
            ]);

            $conversation->messages()->create([
                'user_id' => $user->id,
                'role' => MessageRole::Assistant,
                'content' => $reply,
            ]);

            $conversation->forceFill([
                'last_message_at' => now(),
                'is_active' => true,
                'title' => $conversation->title ?: mb_substr($content, 0, 60),
            ])->save();

            return [
                'conversation_id' => $conversation->id,
                'message' => $reply,
                'remaining_today' => $remaining === null ? null : $remaining - 1,
            ];
        });
    }

    private function resolveConversation(User $user, mixed $conversationId): CoachConversation
    {
        if ($conversationId) {
            $conversation = CoachConversation::query()
                ->where('user_id', $user->id)
                ->whereKey($conversationId)
                ->first();

            if (! $conversation) {
                throw new DomainException('Conversation not found.', 'conversation_not_found');
            }

            return $conversation;
        }

        $existing = CoachConversation::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->latest('last_message_at')
            ->latest('id')
            ->first();

        if ($existing) {
            return $existing;
        }

        return CoachConversation::query()->create([
            'user_id' => $user->id,
            'title' => null,
            'is_active' => true,
        ]);
    }
}
