<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Product identity
    |--------------------------------------------------------------------------
    */
    'name' => env('MENTOR_PRODUCT_NAME', 'AI Mentor Health'),

    'tagline' => env(
        'MENTOR_TAGLINE',
        'Your personal AI wellness coach for habits, fitness, nutrition, sleep, and stress.'
    ),

    /*
    |--------------------------------------------------------------------------
    | Safety / positioning
    |--------------------------------------------------------------------------
    | This product is a wellness coach, not a medical device or clinician.
    */
    'disclaimer' => 'AI Mentor Health provides general wellness guidance and does not diagnose conditions or replace medical professionals.',

    /*
    |--------------------------------------------------------------------------
    | Feature flags
    |--------------------------------------------------------------------------
    | care_marketplace = doctors, appointments, hospitals (deferred)
    | core coaching features stay enabled for MVP.
    */
    'features' => [
        'ai_coach' => env('FEATURE_AI_COACH', true),
        'habit_tracking' => env('FEATURE_HABIT_TRACKING', true),
        'weekly_plans' => env('FEATURE_WEEKLY_PLANS', true),
        'subscriptions' => env('FEATURE_SUBSCRIPTIONS', true),
        'care_marketplace' => env('FEATURE_CARE_MARKETPLACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Plan limits
    |--------------------------------------------------------------------------
    | Free accounts get a daily allowance of AI coach messages, counted from
    | midnight in the user's timezone. Pro (an active or trialing subscription)
    | is unlimited. No limits apply while the subscriptions feature is off,
    | since there would be nothing to upgrade to.
    */
    'limits' => [
        'free_coach_messages_per_day' => (int) env('FREE_COACH_MESSAGES_PER_DAY', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public demo
    |--------------------------------------------------------------------------
    | DEMO_MODE=true is for the hosted demo only:
    | - the AI coach answers with pre-written replies (no provider calls, no cost);
    | - the login page offers "Try the demo", which signs into a shared, pre-filled
    |   account seeded by Database\Seeders\DemoSeeder;
    | - that shared account's password, email and profile can't be changed or deleted.
    */
    'demo' => [
        'enabled' => (bool) env('DEMO_MODE', false),
        'email' => 'demo@ai-mentor-health.test',
    ],
];
