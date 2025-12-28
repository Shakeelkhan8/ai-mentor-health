<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BannerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PartnerController;

use App\Http\Controllers\DiscountController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'front_app.index')->name('home');
Route::view('about', 'front_app.about')->name('about');
Route::view('faqs', 'front_app.faq')->name("faqs");
Route::view('pricing', 'front_app.pricing')->name('pricing');
Route::view('contact', 'front_app.contact')->name('contact');
Route::view('services', 'front_app.services')->name('services');
Route::post('send-mail', [MainController::class, 'send_Mail'])->name("sendmail");

Route::get('/dashboard', function () {
    return view('backend_app.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




// hilview

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [MainController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('community-form', [MainController::class, 'forms'])->name("community-forms");
    // User
    Route::get('edit-profile', [ProfileController::class, 'edit_profile'])->name('edit_profile');
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::POST('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('ai')->group(function () {
        Route::get('chatbot', [AiController::class, 'index'])->name('chatbot');
        Route::post('chatbot-response', [AiController::class, 'get_response'])->name('chatbot-response');
    });

    Route::post('search-nearest-hospitals', [MainController::class, 'searchhospitals'])->name('search-hospitals');
    Route::get('distributor-profile/{id}', [ProfileController::class, 'distributor_profile'])->name('distributor-profile');
    Route::get('client-profile/{id}', [ProfileController::Class, 'client_profile'])->name('client-profile');
    Route::post('update-password', [ProfileController::class, 'update_password'])->name('update-password');
});

//Doctors
Route::prefix('doctors')->group(function () {
    Route::get('all', [DoctorController::class, 'index'])->name('all-doctors');
    Route::get('create', [DoctorController::class, 'create'])->name('create-doctor');
    Route::POST('store', [DoctorController::class, 'store'])->name('store-doctor');
    Route::POST('add-feedback', [DoctorController::class, 'addFeedback'])->name('add-feedback');

    Route::get('edit/{id}', [DoctorController::class, 'edit'])->name('edit-doctor');
    Route::post('update/{id}', [DoctorController::class, 'update'])->name('update-doctor');
    Route::get('delete/{id}', [DoctorController::class, 'destroy'])->name('delete-doctor');
    Route::get('view/{id}', [DoctorController::class, 'show'])->name('show-doctor');
});

Route::resource('appointment', AppointmentController::class);
Route::get('/appointment-success', [AppointmentController::class, 'appointmentSuccess'])->name('appointment.success');
Route::get('/appointment-cancel', [AppointmentController::class, 'appointmentCancel'])->name('appointment.cancel');
Route::get('/all-appointments', [AppointmentController::class, 'show'])->name('appointment.all');


Route::post('form-submit', [MainController::class, 'formSubmit'])->name('contact.store');



require __DIR__ . '/auth.php';
