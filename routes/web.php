<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\Billing\BillingController;
use App\Http\Controllers\Web\Billing\StripeWebhookController;
use App\Http\Controllers\Web\Coaching\CoachController;
use App\Http\Controllers\Web\Habits\HabitDashboardController;
use App\Http\Controllers\Web\Onboarding\OnboardingController;
use App\Http\Controllers\Web\Plans\WeeklyPlanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public marketing pages
|--------------------------------------------------------------------------
*/
Route::view('/', 'front_app.index')->name('home');
Route::view('contact', 'front_app.contact')->name('contact');

// The old one-page-per-section URLs now point at sections of the landing page.
Route::redirect('about', '/#how-it-works')->name('about');
Route::redirect('services', '/#features')->name('services');
Route::redirect('pricing', '/#pricing')->name('pricing');
Route::redirect('faqs', '/#faq')->name('faqs');
Route::middleware('throttle:public-forms')->group(function () {
    Route::post('send-mail', [MainController::class, 'send_Mail'])->name('sendmail');
    Route::post('form-submit', [MainController::class, 'formSubmit'])->name('contact.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated app
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::middleware('onboarded')->group(function () {
        Route::get('/dashboard', [HabitDashboardController::class, 'index'])->name('dashboard');
        Route::post('/habits/{habit}/check-ins', [HabitDashboardController::class, 'store'])
            ->middleware('feature:habit_tracking')
            ->name('habits.check-ins.store');

        Route::get('community-form', [MainController::class, 'forms'])
            ->middleware('can:admin')
            ->name('community-forms');
        Route::get('edit-profile', [ProfileController::class, 'edit_profile'])->name('edit_profile');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        // demo.protect: in demo mode the shared demo account's profile and credentials are read-only.
        Route::middleware('demo.protect')->group(function () {
            Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::patch('/profile', [ProfileController::class, 'update']);
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::post('update-password', [ProfileController::class, 'update_password'])->name('update-password');
        });

        Route::prefix('ai')->middleware(['feature:ai_coach', 'throttle:coach'])->group(function () {
            Route::get('coach', [CoachController::class, 'index'])->name('coach.index');
            Route::post('coach/messages', [CoachController::class, 'store'])->name('coach.messages.store');
            Route::get('chatbot', [CoachController::class, 'index'])->name('chatbot');
            Route::post('chatbot-response', [CoachController::class, 'store'])->name('chatbot-response');
        });

        Route::prefix('plans')->middleware('feature:weekly_plans')->group(function () {
            Route::get('weekly', [WeeklyPlanController::class, 'show'])->name('plans.weekly.show');
            Route::post('weekly/generate', [WeeklyPlanController::class, 'generate'])
                ->middleware('throttle:10,1')
                ->name('plans.weekly.generate');
        });

        Route::prefix('billing')->middleware('feature:subscriptions')->group(function () {
            Route::get('/', [BillingController::class, 'show'])->name('billing.show');
            Route::post('/checkout', [BillingController::class, 'checkout'])
                ->middleware('throttle:10,1')
                ->name('billing.checkout');
            Route::get('/success', [BillingController::class, 'success'])->name('billing.success');
            Route::get('/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
        });
    });
});

Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

require __DIR__.'/auth.php';
