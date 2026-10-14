<?php

namespace App\Infrastructure\Ai;

use App\Domains\Coaching\Contracts\AiCoachClient;

/**
 * AiCoachClient for the public demo (DEMO_MODE=true): answers with pre-written
 * coaching tips picked by topic, so the demo works without an AI provider or cost.
 *
 * Weekly plans expect JSON from the provider; these plain-text replies make
 * WeeklyPlanGenerator use its built-in fallback plan, which is the intended
 * demo behaviour.
 */
class DemoCoachClient implements AiCoachClient
{
    /** @var array<string, list<string>> topic => keywords */
    private const TOPICS = [
        'sleep' => ['sleep', 'tired', 'insomnia', 'bed', 'rest', 'nap'],
        'stress' => ['stress', 'anxious', 'anxiety', 'overwhelm', 'calm', 'relax', 'worry'],
        'nutrition' => ['eat', 'food', 'diet', 'meal', 'nutrition', 'water', 'snack', 'sugar'],
        'fitness' => ['exercise', 'workout', 'run', 'walk', 'gym', 'fitness', 'steps', 'active'],
    ];

    private const REPLIES = [
        'sleep' => 'Good sleep usually comes from consistency. Try going to bed and waking up at the same time every day, '
            .'including weekends, and keep screens out of the last 30 minutes before bed. Checking it in as a daily '
            .'habit on your dashboard makes the pattern easy to see.',
        'stress' => 'When stress builds up, a short reset helps: try box breathing (in for 4, hold 4, out 4, hold 4) for two '
            .'minutes, then write down the one thing you can act on today. Small, regular breaks work better than one long one.',
        'nutrition' => 'A simple win is to build each meal around a protein, a vegetable and some fibre, and keep a water bottle '
            .'where you can see it. Changing one meal at a time is easier to stick with than a full diet overhaul.',
        'fitness' => 'Consistency beats intensity: a 10-minute walk after one meal a day is a strong start. Once that feels easy '
            .'for a week, add a few minutes or a second walk rather than jumping to long workouts.',
        'general' => 'A good next step is to pick one small habit you can do every day this week, check it in on your dashboard, '
            .'and review how it went on the weekend. Ask me about sleep, stress, nutrition or fitness for specific ideas.',
    ];

    public function complete(array $messages, array $options = []): string
    {
        $last = strtolower((string) (collect($messages)->last(fn ($m) => $m['role'] === 'user')['content'] ?? ''));

        foreach (self::TOPICS as $topic => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($last, $keyword)) {
                    return self::REPLIES[$topic];
                }
            }
        }

        return self::REPLIES['general'];
    }
}
