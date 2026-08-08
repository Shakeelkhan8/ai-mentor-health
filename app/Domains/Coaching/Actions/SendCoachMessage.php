<?php

namespace App\Domains\Coaching\Actions;

use App\Domains\Coaching\Enums\MessageRole;
use App\Domains\Coaching\Models\CoachConversation;
use App\Domains\Coaching\Models\CoachMessage;
use App\Domains\Coaching\Services\CoachingService;
use App\Domains\Shared\Actions\Action;
use App\Domains\Shared\Exceptions\DomainException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendCoachMessage extends Action
{
    private const HISTORY_LIMIT = 20;

    public function __construct(
        private readonly CoachingService $coachingService,
    ) {
    }

    /**
     * @return array{conversation_id: int, message: string}
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

        // Persist the user turn first — never hold a DB transaction open during the AI HTTP call.
        [$conversation, $history] = DB::transaction(function () use ($user, $content, $conversationId) {
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

            $conversation->messages()->create([
                'user_id' => $user->id,
                'role' => MessageRole::User,
                'content' => $content,
            ]);

            return [$conversation, $history];
        });

        try {
            $reply = $this->coachingService->reply($content, $history);
        } catch (Throwable $e) {
            report($e);

            if ($e instanceof DomainException) {
                throw $e;
            }

            throw new DomainException(
                'Unable to reach the AI coaching provider.',
                'ai_provider_error',
                previous: $e,
            );
        }

        DB::transaction(function () use ($conversation, $user, $content, $reply) {
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
        });

        return [
            'conversation_id' => $conversation->id,
            'message' => $reply,
        ];
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
