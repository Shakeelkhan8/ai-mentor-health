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
        'care_marketplace' => env('FEATURE_CARE_MARKETPLACE', false), // keep false — deferred & payment path hardened only
    ],
];
