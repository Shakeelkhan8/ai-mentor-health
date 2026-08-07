<?php

namespace App\Domains\Coaching\Services;

use App\Domains\Coaching\Contracts\AiCoachClient;

/**
 * Application service for coaching conversations.
 * Controllers call this — not the AI HTTP client directly.
 */
class CoachingService
{
    public function __construct(
        private readonly AiCoachClient $aiCoachClient,
    ) {
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function reply(string $userMessage, array $history = []): string
    {
        $system = (string) config('mentor.disclaimer');

        $messages = array_merge(
            [
                [
                    'role' => 'system',
                    'content' => 'You are AI Mentor Health, a supportive wellness and lifestyle coach. '
                        .'Help with fitness, nutrition, sleep, stress, and healthy habits. '
                        .'You do not diagnose medical conditions or prescribe treatment. '
                        .'If the user describes an emergency or severe symptoms, tell them to seek professional medical care. '
                        ."Disclaimer: {$system}",
                ],
            ],
            $history,
            [
                ['role' => 'user', 'content' => $userMessage],
            ],
        );

        return $this->aiCoachClient->complete($messages);
    }
}
