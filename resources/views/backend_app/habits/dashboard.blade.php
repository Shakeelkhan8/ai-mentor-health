@php
    use App\Domains\Habits\Enums\CheckInStatus;

    $stats = [
        ['Today’s completion', $completion_percent.'%', $completed_today.'/'.$total_active.' habits done'],
        ['Active habits', $total_active, 'From your onboarding focus'],
        ['This week', $week_done_count, 'Completed check-ins'],
        ['Best streak', $longest_streak, 'Consecutive done days'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $productName }} Dashboard</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Today ({{ $today }}) · {{ $timezone }}
                    @if($profile?->primary_goal)
                        · Goal: {{ $profile->primary_goal }}
                    @endif
                </p>
            </div>
            @if (config('mentor.features.ai_coach'))
                <a href="{{ route('coach.index') }}" class="btn-primary">
                    Talk to AI Coach
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <x-alert type="error">{{ $errors->first() }}</x-alert>
            @endif

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($stats as [$label, $value, $note])
                    <x-card>
                        <div class="text-sm text-gray-500">{{ $label }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $value }}</div>
                        <div class="text-sm text-gray-600">{{ $note }}</div>
                    </x-card>
                @endforeach
            </div>

            <x-card>
                <h3 class="text-lg font-medium text-gray-900">Today’s habits</h3>

                <div class="mt-4 space-y-3">
                    @forelse ($habits as $habit)
                        @php
                            $todayStatus = $habit->today_check_in?->status;
                        @endphp
                        <div class="flex flex-wrap justify-between gap-4 rounded-md border border-gray-200 p-4">
                            <div>
                                <div class="font-medium text-gray-900">{{ $habit->name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $habit->focus_area->label() }}
                                    · streak {{ $habit->current_streak }} day{{ $habit->current_streak === 1 ? '' : 's' }}
                                </div>
                                @if($habit->description)
                                    <div class="mt-1 text-sm text-gray-700">{{ $habit->description }}</div>
                                @endif
                                <div class="mt-2">
                                    @if($todayStatus === CheckInStatus::Done)
                                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Done today</span>
                                    @elseif($todayStatus === CheckInStatus::Partial)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Partial today</span>
                                    @elseif($todayStatus === CheckInStatus::Skipped)
                                        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-700">Skipped today</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Not checked in</span>
                                    @endif
                                </div>
                            </div>

                            @if (config('mentor.features.habit_tracking'))
                                <div class="flex flex-wrap items-start gap-2">
                                    @foreach (['done' => 'Done', 'partial' => 'Partial', 'skipped' => 'Skip'] as $status => $label)
                                        <form method="POST" action="{{ route('habits.check-ins.store', $habit) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" @class([
                                                'rounded-md px-3 py-1.5 text-sm font-medium',
                                                'bg-green-600 text-white hover:bg-green-700' => $status === 'done',
                                                'border border-gray-300 text-gray-700 hover:bg-gray-50' => $status !== 'done',
                                            ])>{{ $label }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No active habits yet. Complete onboarding to generate starter habits.</p>
                    @endforelse
                </div>
            </x-card>

            <x-alert type="warning">{{ $disclaimer }}</x-alert>
        </div>
    </div>
</x-app-layout>
