@php
    $freeLimit = config('mentor.limits.free_coach_messages_per_day');
    $demo = config('mentor.demo.enabled');

    $features = [
        ['AI coach', 'Ask about sleep, stress, nutrition or fitness. The coach knows your goal and focus areas, and keeps the conversation history.', 'M8 10h8M8 14h5m-9 6 3-3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H6a3 3 0 0 0-3 3v13Z'],
        ['Daily habit check-ins', 'Mark each habit done, partial or skipped. Completion and streaks update the moment you check in.', 'm5 13 4 4L19 7'],
        ['Weekly plans', 'A plan for the week built from your goal, focus areas, habits and recent check-ins. Regenerate it any time.', 'M8 3v3m8-3v3M4 9h16M5 6h14a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1Z'],
        ['Pro when you need more', "Free includes {$freeLimit} coach messages a day. Pro removes the limit, with secure checkout through Stripe.", 'M12 3 4 7v5c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V7l-8-4Z'],
    ];

    $steps = [
        ['Tell us your goal', 'Pick your focus areas and timezone. Starter habits are created for you.'],
        ['Talk to your coach', 'Get practical, goal-aware guidance whenever you need it.'],
        ['Check in daily', 'Two taps a day keep your habits and streaks up to date.'],
        ['Follow your weekly plan', 'A fresh plan each week, shaped by how the last one went.'],
    ];

    $faqs = [
        ['Is this medical advice?', config('mentor.disclaimer').' If you have a health concern, talk to a qualified professional.'],
        ['How does the free limit work?', "Free accounts get {$freeLimit} AI coach messages a day. The count resets at midnight in your own timezone. Pro accounts have no daily limit."],
        ['Does the coach remember earlier conversations?', 'Yes. Your conversation history and profile are saved to your account, so the coach can keep the context of your goal.'],
        ['How is payment handled?', 'Pro checkout runs on Stripe. Card details go to Stripe directly and are never stored by AI Mentor Health.'],
    ];
@endphp

<x-marketing-layout>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-ink">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(70rem_40rem_at_50%_-10%,rgba(16,185,133,0.25),transparent_65%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.035)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.035)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_at_top,black_40%,transparent_75%)]"></div>

        <div class="relative mx-auto max-w-6xl px-6 pb-0 pt-20 text-center sm:pt-28">
            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-medium text-brand-200">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                Your personal AI wellness coach
            </span>

            <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-semibold tracking-tight text-white sm:text-6xl sm:leading-[1.05]">
                Build healthier habits with a coach that <span class="bg-gradient-to-r from-brand-300 to-brand-500 bg-clip-text text-transparent">remembers you</span>.
            </h1>

            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-gray-300">
                Coaching, daily check-ins and a weekly plan for sleep, stress, nutrition and fitness, all built around your own goal.
            </p>

            <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
                @if ($demo)
                    <form method="POST" action="{{ route('demo.login') }}">
                        @csrf
                        <button type="submit" class="btn-primary px-6 py-3 text-base">Try the demo</button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-3 text-base">Get started free</a>
                @endif
                <a href="#how-it-works" class="btn px-6 py-3 text-base text-gray-200 ring-1 ring-inset ring-white/15 hover:bg-white/5">See how it works</a>
            </div>
            <p class="mt-4 text-xs text-gray-500">
                {{ $demo ? 'Opens a shared demo account. No signup needed.' : "Free plan includes {$freeLimit} coach messages a day." }}
            </p>

            {{-- Product shot --}}
            <div class="relative mx-auto mt-16 max-w-5xl">
                <div class="absolute -inset-x-10 -top-10 bottom-0 rounded-[2rem] bg-brand-500/20 blur-3xl"></div>
                <div class="relative overflow-hidden rounded-t-2xl border border-b-0 border-white/10 bg-ink-soft shadow-2xl">
                    <div class="flex items-center gap-1.5 border-b border-white/10 px-4 py-3">
                        <span class="h-2.5 w-2.5 rounded-full bg-white/15"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-white/15"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-white/15"></span>
                    </div>
                    <img src="{{ asset('images/product/dashboard.png') }}" width="1280" height="800" alt="The AI Mentor Health dashboard with today's habits, completion and streaks" class="block w-full">
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="scroll-mt-16 py-24">
        <div class="mx-auto max-w-6xl px-6">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold text-brand-600">Features</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Everything you need to change one habit at a time.</h2>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($features as [$name, $text, $icon])
                    <div class="rounded-2xl border border-gray-200 p-6 transition hover:border-brand-200 hover:shadow-lg hover:shadow-brand-500/5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-inset ring-brand-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="{{ $icon }}"/></svg>
                        </div>
                        <h3 class="mt-5 font-semibold">{{ $name }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Coach and plan --}}
    <section class="bg-gray-50 py-24">
        <div class="mx-auto grid max-w-6xl items-center gap-16 px-6 lg:grid-cols-2">
            <div>
                <p class="text-sm font-semibold text-brand-600">AI coach</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight">Advice that starts from your goal, not a blank page.</h2>
                <p class="mt-4 leading-relaxed text-gray-600">
                    The coach sees your goal and focus areas, so a question like "why am I always tired?" gets an answer about your sleep plan, not a generic article.
                </p>
            </div>
            <img src="{{ asset('images/product/coach.png') }}" width="1280" height="800" alt="A coaching conversation about sleep and work stress" class="w-full rounded-2xl border border-gray-200 shadow-xl shadow-gray-900/5">
        </div>

        <div class="mx-auto mt-24 grid max-w-6xl items-center gap-16 px-6 lg:grid-cols-2">
            <img src="{{ asset('images/product/weekly-plan.png') }}" width="1280" height="800" alt="A weekly plan with sleep, mindset, fitness and habit goals" class="w-full rounded-2xl border border-gray-200 shadow-xl shadow-gray-900/5 lg:order-first">
            <div class="order-first lg:order-none">
                <p class="text-sm font-semibold text-brand-600">Weekly plan</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight">A plan for the week, shaped by the last one.</h2>
                <p class="mt-4 leading-relaxed text-gray-600">
                    Each plan uses your goal, your habits and your recent check-ins, and turns them into a short list of concrete targets you can actually hit.
                </p>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="scroll-mt-16 py-24">
        <div class="mx-auto max-w-6xl px-6">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold text-brand-600">How it works</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Four steps, a few minutes a day.</h2>
            </div>

            <ol class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $i => [$name, $text])
                    <li class="relative border-t border-gray-200 pt-6">
                        <span class="font-mono text-sm text-brand-600">0{{ $i + 1 }}</span>
                        <h3 class="mt-3 font-semibold">{{ $name }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="scroll-mt-16 bg-gray-50 py-24">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-semibold text-brand-600">Pricing</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Start free. Upgrade when you want more.</h2>
            </div>

            <div class="mx-auto mt-14 grid max-w-4xl gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-8">
                    <h3 class="font-semibold">Free</h3>
                    <p class="mt-2 text-sm text-gray-600">Everything you need to build the habit.</p>
                    <ul class="mt-8 space-y-3 text-sm">
                        @foreach (['Personal onboarding and starter habits', 'Daily habit check-ins and streaks', 'Weekly plans', "{$freeLimit} AI coach messages a day"] as $item)
                            <li class="flex gap-3"><svg class="h-5 w-5 shrink-0 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>{{ $item }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="btn-secondary mt-8 w-full">Create a free account</a>
                </div>

                <div class="relative overflow-hidden rounded-2xl bg-ink p-8 text-white ring-1 ring-brand-500/30">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(30rem_20rem_at_100%_0%,rgba(16,185,133,0.25),transparent_60%)]"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold">Pro</h3>
                            <span class="rounded-full bg-brand-500/15 px-2.5 py-0.5 text-xs font-medium text-brand-300">Subscription</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-400">For when you want the coach on hand all day.</p>
                        <ul class="mt-8 space-y-3 text-sm text-gray-200">
                            @foreach (['Everything in Free', 'Unlimited AI coach messages', 'Secure checkout with Stripe'] as $item)
                                <li class="flex gap-3"><svg class="h-5 w-5 shrink-0 text-brand-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn-primary mt-8 w-full">Start with Free, upgrade any time</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="scroll-mt-16 py-24">
        <div class="mx-auto max-w-3xl px-6">
            <div class="text-center">
                <p class="text-sm font-semibold text-brand-600">FAQ</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Questions, answered.</h2>
            </div>

            <div class="mt-12 divide-y divide-gray-200 border-y border-gray-200">
                @foreach ($faqs as [$q, $a])
                    <details class="group py-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-6 font-medium">
                            {{ $q }}
                            <svg class="h-5 w-5 shrink-0 text-gray-400 transition group-open:rotate-45" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Closing call to action --}}
    <section class="px-6 pb-24">
        <div class="relative mx-auto max-w-6xl overflow-hidden rounded-3xl bg-ink px-8 py-16 text-center">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(40rem_20rem_at_50%_120%,rgba(16,185,133,0.3),transparent_70%)]"></div>
            <div class="relative">
                <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">Start with one habit this week.</h2>
                <p class="mx-auto mt-4 max-w-xl text-gray-300">Set your goal in under a minute and get your first plan today.</p>
                <a href="{{ route('register') }}" class="btn-primary mt-8 px-6 py-3 text-base">Get started free</a>
            </div>
        </div>
    </section>
</x-marketing-layout>
