<?php

namespace App\Http\Controllers\Web\Coaching;

use App\Domains\Coaching\Actions\SendCoachMessage;
use App\Domains\Coaching\Models\CoachConversation;
use App\Domains\Shared\Exceptions\DomainException;
use App\Http\Controllers\Web\WebController;
use App\Http\Requests\Coaching\SendCoachMessageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;

class CoachController extends WebController
{
    public function index(): View
    {
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
            ]);
        } catch (DomainException $e) {
            $status = match ($e->errorCode) {
                'ai_not_configured', 'ai_provider_error' => 503,
                'conversation_not_found' => 404,
                default => 422,
            };

            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->errorCode,
            ], $status);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'error' => 'Unable to get a coaching response right now. Please try again.',
                'code' => 'coach_unavailable',
            ], 500);
        }
    }
}
