<?php

namespace App\Domains\Plans\Services;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Domains\Habits\Enums\CheckInStatus;
use App\Domains\Habits\Models\Habit;
use App\Domains\Habits\Models\HabitCheckIn;
use App\Domains\Identity\Enums\HealthFocusArea;
use App\Domains\Plans\Models\WeeklyPlan;
use App\Models\User;
use Carbon\Carbon;
use Throwable;

class WeeklyPlanGenerator
{
    public const PLAN_DISCLAIMER = 'This plan provides general wellness guidance and is not medical advice or a diagnosis.';

    private const CATEGORIES = ['sleep', 'nutrition', 'fitness', 'mindset', 'habit'];

    public function __construct(
        private readonly AiCoachClient $aiCoachClient,
    ) {
    }

    /**
     * @return array{title: string, summary: string, items: list<array{action: string, category: string, target: string}>, source: string}
     */
    public function generate(User $user, string $weekStart, ?WeeklyPlan $existing = null): array
    {
        $context = $this->buildContext($user, $weekStart, $existing);

        try {
            $raw = $this->aiCoachClient->complete($this->messages($context));
            $parsed = $this->parseAndValidate($raw);
            if ($parsed !== null) {
                return [...$parsed, 'source' => 'ai'];
            }
        } catch (Throwable) {
            // Fall through to template — AI unavailable / misconfigured / provider errors.
        }

        return [...$this->fallbackPlan($context), 'source' => 'fallback'];
    }

    /**
     * @return array{
     *   primary_goal: string|null,
     *   focus_areas: list<string>,
     *   habits: list<string>,
     *   check_in_patterns: array{done: int, skipped: int, partial: int, days: int},
     *   previous_plan: array{title: string|null, summary: string|null, items: array}|null
     * }
     */
    public function buildContext(User $user, string $weekStart, ?WeeklyPlan $existing = null): array
    {
        $profile = $user->profile;
        $focusAreas = collect($profile?->focus_areas ?? [])
            ->map(fn ($area) => is_string($area) ? $area : (string) $area)
            ->filter()
            ->values()
            ->all();

        $habits = Habit::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['name', 'focus_area', 'description']);

        $since = Carbon::parse($weekStart)->subDays(7)->toDateString();
        $checkIns = HabitCheckIn::query()
            ->where('user_id', $user->id)
            ->whereDate('check_in_date', '>=', $since)
            ->get(['status']);

        $patterns = [
            'done' => $checkIns->where('status', CheckInStatus::Done)->count(),
            'skipped' => $checkIns->where('status', CheckInStatus::Skipped)->count(),
            'partial' => $checkIns->where('status', CheckInStatus::Partial)->count(),
            'days' => 7,
        ];

        $previous = null;
        if ($existing !== null) {
            $previous = [
                'title' => $existing->title,
                'summary' => $existing->summary,
                'items' => $existing->items ?? [],
            ];
        }

        return [
            'primary_goal' => $profile?->primary_goal,
            'focus_areas' => $focusAreas,
            'habits' => $habits->map(function (Habit $habit) {
                $focus = $habit->focus_area instanceof HealthFocusArea
                    ? $habit->focus_area->value
                    : (string) $habit->focus_area;

                return trim($habit->name.($focus ? " ({$focus})" : ''));
            })->all(),
            'check_in_patterns' => $patterns,
            'previous_plan' => $previous,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return list<array{role: string, content: string}>
     */
    private function messages(array $context): array
    {
        $schema = json_encode([
            'title' => 'string',
            'summary' => 'string',
            'items' => [
                [
                    'action' => 'string',
                    'category' => 'sleep|nutrition|fitness|mindset|habit',
                    'target' => 'string',
                ],
            ],
        ], JSON_UNESCAPED_SLASHES);

        $contextJson = json_encode($context, JSON_UNESCAPED_SLASHES);

        return [
            [
                'role' => 'system',
                'content' => 'You are AI Mentor Health, a wellness coach. '
                    .'Create a practical weekly wellness plan. '
                    .'Do not diagnose, prescribe medication, or give medical treatment advice. '
                    .'Respond with ONLY valid JSON matching this schema (no markdown): '.$schema
                    .' Priority for personalization: 1) primary_goal 2) focus_areas 3) habits 4) check_in_patterns 5) previous_plan. '
                    .'Include 3 to 7 items. '.self::PLAN_DISCLAIMER,
            ],
            [
                'role' => 'user',
                'content' => "Generate this week's plan from context:\n{$contextJson}",
            ],
        ];
    }

    /**
     * @return array{title: string, summary: string, items: list<array{action: string, category: string, target: string}>}|null
     */
    public function parseAndValidate(string $raw): ?array
    {
        $decoded = $this->decodeJsonPayload($raw);
        if ($decoded === null) {
            return null;
        }

        return $this->validateSchema($decoded);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJsonPayload(string $raw): ?array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/is', $raw, $matches)) {
            $raw = $matches[1];
        } elseif (preg_match('/\{.*\}/s', $raw, $matches)) {
            $raw = $matches[0];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return null;
        }

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{title: string, summary: string, items: list<array{action: string, category: string, target: string}>}|null
     */
    public function validateSchema(array $data): ?array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $summary = trim((string) ($data['summary'] ?? ''));
        $rawItems = $data['items'] ?? null;

        if ($title === '' || $summary === '' || ! is_array($rawItems)) {
            return null;
        }

        $items = [];
        foreach ($rawItems as $item) {
            if (! is_array($item)) {
                continue;
            }

            $action = trim((string) ($item['action'] ?? ''));
            $category = strtolower(trim((string) ($item['category'] ?? '')));
            $target = trim((string) ($item['target'] ?? ''));

            if ($action === '' || $target === '' || ! in_array($category, self::CATEGORIES, true)) {
                continue;
            }

            $items[] = [
                'action' => mb_substr($action, 0, 200),
                'category' => $category,
                'target' => mb_substr($target, 0, 200),
            ];
        }

        if ($items === []) {
            return null;
        }

        return [
            'title' => mb_substr($title, 0, 160),
            'summary' => mb_substr($summary, 0, 1000),
            'items' => array_slice($items, 0, 7),
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array{title: string, summary: string, items: list<array{action: string, category: string, target: string}>}
     */
    public function fallbackPlan(array $context): array
    {
        $goal = trim((string) ($context['primary_goal'] ?? ''));
        $focusAreas = $context['focus_areas'] ?? [];
        $habits = $context['habits'] ?? [];

        $title = $goal !== ''
            ? 'Weekly plan: '.$goal
            : 'Your weekly wellness plan';

        $summary = $goal !== ''
            ? "A practical week focused on \"{$goal}\", built from your profile when AI is unavailable."
            : 'A practical wellness week based on your focus areas and habits. AI generation was unavailable, so a safe template was used.';

        $items = [];

        foreach (array_slice($focusAreas, 0, 3) as $area) {
            $items[] = $this->fallbackItemForFocus((string) $area);
        }

        foreach (array_slice($habits, 0, 2) as $habitLabel) {
            $name = preg_replace('/\s*\([^)]*\)\s*$/', '', (string) $habitLabel) ?: 'your habit';
            $items[] = [
                'action' => "Stay consistent with {$name}",
                'category' => 'habit',
                'target' => 'Complete it on at least 5 days this week',
            ];
        }

        if ($items === []) {
            $items = [
                [
                    'action' => 'Protect a consistent bedtime wind-down',
                    'category' => 'sleep',
                    'target' => 'Start winding down 30 minutes earlier on 5 nights',
                ],
                [
                    'action' => 'Take a short daily movement break',
                    'category' => 'fitness',
                    'target' => 'Walk or stretch for 15 minutes on 5 days',
                ],
                [
                    'action' => 'Practice a brief stress reset',
                    'category' => 'mindset',
                    'target' => 'Do 5 minutes of calm breathing on 4 days',
                ],
            ];
        }

        return [
            'title' => mb_substr($title, 0, 160),
            'summary' => mb_substr($summary, 0, 1000),
            'items' => array_slice($items, 0, 7),
        ];
    }

    /**
     * @return array{action: string, category: string, target: string}
     */
    private function fallbackItemForFocus(string $area): array
    {
        return match ($area) {
            HealthFocusArea::Sleep->value => [
                'action' => 'Improve sleep consistency',
                'category' => 'sleep',
                'target' => 'Same bedtime window on at least 5 nights',
            ],
            HealthFocusArea::Nutrition->value => [
                'action' => 'Build a simple nutrition routine',
                'category' => 'nutrition',
                'target' => 'Include one balanced meal intentionally each day',
            ],
            HealthFocusArea::Fitness->value => [
                'action' => 'Add sustainable movement',
                'category' => 'fitness',
                'target' => 'Move intentionally for 20+ minutes on 4 days',
            ],
            HealthFocusArea::Stress->value => [
                'action' => 'Reduce daily stress load',
                'category' => 'mindset',
                'target' => 'Use a 5-minute reset on 5 days',
            ],
            HealthFocusArea::Preventive->value => [
                'action' => 'Support preventive wellness habits',
                'category' => 'habit',
                'target' => 'Complete one preventive habit check-in daily',
            ],
            default => [
                'action' => 'Reinforce a healthy daily habit',
                'category' => 'habit',
                'target' => 'Complete one wellness action daily',
            ],
        };
    }
}
