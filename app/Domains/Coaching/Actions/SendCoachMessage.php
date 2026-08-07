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

        return DB::transaction(function () use ($user, $content, $conversationId) {
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

            $reply = $this->coachingService->reply($content, $history);

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
