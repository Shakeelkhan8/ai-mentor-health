<?php

namespace App\Http\Controllers\Web\Coaching;

use App\Domains\Coaching\Actions\SendCoachMessage;
use App\Domains\Coaching\Models\CoachConversation;
use App\Domains\Coaching\Services\CoachUsage;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Coaching\SendCoachMessageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

class CoachController extends WebController
{
    public function index(CoachUsage $usage): View
    {
        $user = auth()->user();

        $conversation = CoachConversation::query()
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->with(['messages' => fn ($q) => $q->orderBy('id')])
            ->latest('last_message_at')
            ->latest('id')
            ->first();

        return view('backend_app.chatbot.index', [
            'conversation' => $conversation,
            'disclaimer' => config('mentor.disclaimer'),
            'productName' => config('mentor.name'),
            'dailyLimit' => $usage->dailyLimit($user),
            'remainingToday' => $usage->remainingToday($user),
        ]);
    }

    public function store(
        SendCoachMessageRequest $request,
        SendCoachMessage $sendCoachMessage,
    ): JsonResponse {
        try {
            $result = $sendCoachMessage->execute(
                $request->user(),
                $request->validated('message'),
                $request->validated('conversation_id'),
            );

            return response()->json([
                'conversation_id' => $result['conversation_id'],
                'message' => $result['message'],
                'remaining_today' => $result['remaining_today'],
            ]);
        } catch (DomainException $e) {
            $status = match ($e->errorCode) {
                'ai_not_configured', 'ai_provider_error' => 503,
                'conversation_not_found' => 404,
                'coach_daily_limit_reached' => 429,
                default => 422,
            };

            $message = $e->errorCode === 'ai_provider_error'
                ? "Your coach is temporarily unavailable, so your message wasn't sent. Please try again in a minute."
                : $e->getMessage();

            return response()->json(array_filter([
                'error' => $message,
                'code' => $e->errorCode,
                'upgrade_url' => $e->errorCode === 'coach_daily_limit_reached' ? route('billing.show') : null,
            ]), $status);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'error' => 'Unable to get a coaching response right now. Please try again.',
                'code' => 'coach_unavailable',
            ], 500);
        }
    }
}
