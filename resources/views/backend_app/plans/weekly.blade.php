<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Weekly Plan</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Week of {{ $weekStart }} · {{ $timezone }}
                    @if($profile?->primary_goal)
                        · Goal: {{ $profile->primary_goal }}
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('plans.weekly.generate') }}">
                @csrf
                <x-primary-button>{{ $plan ? 'Regenerate plan' : 'Generate plan' }}</x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <x-alert type="error">{{ $errors->first() }}</x-alert>
            @endif

            <x-alert type="warning">{{ $planDisclaimer }}</x-alert>

            @if (! ($hasActiveSubscription ?? false) && config('mentor.features.subscriptions'))
                <x-alert type="info" class="flex flex-wrap items-center justify-between gap-2">
                    <span>Enjoying personalized plans? Pro checkout is available in test mode — nothing is locked yet.</span>
                    <a href="{{ route('billing.show') }}" class="font-medium underline">View Pro</a>
                </x-alert>
            @endif

            <x-card>
                @if ($plan)
                    <h3 class="text-lg font-medium text-gray-900">{{ $plan->title }}</h3>
                    <p class="mt-2 text-gray-700">{{ $plan->summary }}</p>
                    <p class="mt-2 text-sm text-gray-500">
                        Status: {{ $plan->status->value }}
                        @if($plan->generated_at)
                            · Generated {{ $plan->generated_at->diffForHumans() }}
                        @endif
                    </p>

                    <ul class="mt-4 divide-y divide-gray-200 rounded-md border border-gray-200">
                        @foreach (($plan->items ?? []) as $item)
                            <li class="flex flex-wrap justify-between gap-2 p-4">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $item['action'] ?? '' }}</div>
                                    <div class="text-sm text-gray-500">{{ $item['target'] ?? '' }}</div>
                                </div>
                                <span class="self-start rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium uppercase text-brand-700">
                                    {{ $item['category'] ?? 'habit' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">
                        No plan for this week yet. Generate a personalized plan based on your goals, focus areas, and habits.
                    </p>
                @endif
            </x-card>

            <p class="text-sm text-gray-500">{{ $disclaimer }}</p>
        </div>
    </div>
</x-app-layout>
