<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Welcome to {{ $productName }}</h2>
        <p class="mt-1 text-sm text-gray-600">
            Tell us what you want to improve. We’ll personalize your coaching focus and create starter habits.
        </p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <x-alert type="warning">{{ $disclaimer }}</x-alert>

            @if ($errors->any())
                <x-alert type="error">
                    <ul class="list-disc ps-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <x-card>
                <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-6">
                    @csrf

                    <fieldset>
                        <legend class="block font-medium text-sm text-gray-700">What do you want to focus on?</legend>
                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            @foreach ($focusAreas as $area)
                                <label for="focus_{{ $area->value }}" class="flex items-center gap-3 rounded-md border border-gray-200 p-3 hover:bg-gray-50">
                                    <input
                                        type="checkbox"
                                        class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                        name="focus_areas[]"
                                        id="focus_{{ $area->value }}"
                                        value="{{ $area->value }}"
                                        @checked(collect(old('focus_areas', $profile?->focus_areas ?? []))->contains($area->value))
                                    >
                                    <span class="text-sm text-gray-800">{{ $area->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div>
                        <x-input-label for="primary_goal" value="Your primary goal" />
                        <textarea
                            id="primary_goal"
                            name="primary_goal"
                            rows="3"
                            maxlength="500"
                            class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm"
                            placeholder="Example: Build a consistent sleep routine and reduce late-night scrolling."
                            required
                        >{{ old('primary_goal', $profile?->primary_goal) }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="timezone" value="Timezone" />
                        <select id="timezone" name="timezone" class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                            @foreach ($timezones as $timezone)
                                <option value="{{ $timezone }}" @selected($timezone === old('timezone', $selectedTimezone))>
                                    {{ $timezone }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Used for daily check-ins and reminders later.</p>
                    </div>

                    <x-primary-button>Start coaching</x-primary-button>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
