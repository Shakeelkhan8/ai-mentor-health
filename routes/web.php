<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FormController;
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
Route::view('about', 'front_app.about')->name('about');
Route::view('faqs', 'front_app.faq')->name('faqs');
Route::view('pricing', 'front_app.pricing')->name('pricing');
Route::view('contact', 'front_app.contact')->name('contact');
Route::view('services', 'front_app.services')->name('services');
Route::post('send-mail', [MainController::class, 'send_Mail'])
    ->middleware('throttle:5,1')
    ->name('sendmail');
Route::post('form-submit', [MainController::class, 'formSubmit'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

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

        Route::get('community-form', [MainController::class, 'forms'])->name('community-forms');
        Route::get('edit-profile', [ProfileController::class, 'edit_profile'])->name('edit_profile');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('update-password', [ProfileController::class, 'update_password'])->name('update-password');
        Route::get('distributor-profile/{id}', [ProfileController::class, 'distributor_profile'])->name('distributor-profile');
        Route::get('client-profile/{id}', [ProfileController::class, 'client_profile'])->name('client-profile');

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

/*
|--------------------------------------------------------------------------
| Deferred: care marketplace (doctors / appointments / hospitals)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'onboarded', 'feature:care_marketplace'])->group(function () {
    Route::post('search-nearest-hospitals', [MainController::class, 'searchhospitals'])->name('search-hospitals');

    Route::prefix('doctors')->group(function () {
        Route::get('all', [DoctorController::class, 'index'])->name('all-doctors');
        Route::get('create', [DoctorController::class, 'create'])->name('create-doctor');
        Route::post('store', [DoctorController::class, 'store'])->name('store-doctor');
        Route::post('add-feedback', [DoctorController::class, 'addFeedback'])->name('add-feedback');
        Route::get('edit/{id}', [DoctorController::class, 'edit'])->name('edit-doctor');
        Route::post('update/{id}', [DoctorController::class, 'update'])->name('update-doctor');
        Route::get('delete/{id}', [DoctorController::class, 'destroy'])->name('delete-doctor');
        Route::get('view/{id}', [DoctorController::class, 'show'])->name('show-doctor');
    });

    Route::resource('appointment', AppointmentController::class);
    Route::get('/appointment-success', [AppointmentController::class, 'appointmentSuccess'])->name('appointment.success');
    Route::get('/appointment-cancel', [AppointmentController::class, 'appointmentCancel'])->name('appointment.cancel');
    Route::get('/all-appointments', [AppointmentController::class, 'show'])->name('appointment.all');
});

require __DIR__.'/auth.php';
