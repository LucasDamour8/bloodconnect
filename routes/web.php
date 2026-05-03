<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EligibilityController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
// Added the two new controllers you requested
use App\Http\Controllers\DonorLocationController;
use App\Http\Controllers\DonorEligibilityController;
use App\Models\Announcement;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

/**
 * Landing Page: Uses the HomeController to handle announcements and stats
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

// routes/web.php
Route::get('/learn', [App\Http\Controllers\LocationController::class, 'learn'])->name('learn.index');

// Tracking Route for the "Track Status" button in the index modal
Route::get('/appointments/track', [HomeController::class, 'track'])->name('appointments.track');

// Public Feedback Submission (Reach Us form)
Route::post('/feedback/store', [HomeController::class, 'feedback'])->name('feedback.store');

/*
|--------------------------------------------------------------------------
| LOCALIZATION ROUTE
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');

// Eligibility Quiz
Route::get('/eligibility',        [EligibilityController::class, 'index'])->name('eligibility.index');
Route::post('/eligibility/check', [EligibilityController::class, 'check'])->name('eligibility.check');

// Donation Centers read-only
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');


/*
|--------------------------------------------------------------------------
| PASSWORD RESET & EMAIL VERIFICATION
|--------------------------------------------------------------------------
*/
// Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification
Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| GUEST ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',     [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login',    [LoginController::class, 'login']);
    Route::get('/register',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| SOCIAL LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/auth/{provider}',           [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback',  [SocialAuthController::class, 'callback'])->name('social.callback');

/*
|--------------------------------------------------------------------------
| SHARED AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/api/available-dates/{location_id}', [AppointmentController::class, 'getAvailableDates'])->name('api.available-dates');
    Route::get('/api/slots', [AppointmentController::class, 'getSlots'])->name('api.slots');
    Route::get('/api/available-dates/{location_id}', [DonorLocationController::class, 'getAvailableDates'])->name('api.available-dates');
});
// Public access to Eligibility
Route::get('/eligibility', [EligibilityController::class, 'index'])->name('eligibility.index');
Route::post('/eligibility/check', [EligibilityController::class, 'check'])->name('eligibility.check');

// Public access to Locations
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

/*
|--------------------------------------------------------------------------
| DONOR ROUTES
|--------------------------------------------------------------------------
*/    
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::middleware(['auth', 'role:donor'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/donor/book', [DonorLocationController::class, 'store'])->name('donor.appointments.store');

    // Appointments
    Route::get('/appointments/book',            [AppointmentController::class, 'create'])->name('appointments.create');
    Route::get('/appointments',                 [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments',                [AppointmentController::class, 'store'])->name('appointments.store');
    
    Route::get('/appointments/{id}/edit',       [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::delete('/appointments/{id}',         [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::patch('/appointments/{id}',          [AppointmentController::class, 'update'])->name('appointments.update');
    Route::patch('/appointments/{id}/cancel',   [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/{id}',            [AppointmentController::class, 'show'])->name('appointments.show');

    // New Donor Routes Integrated Here
    Route::get('/donor/locations', [DonorLocationController::class, 'index'])->name('donor.locations');
    Route::get('/donor/eligibility', [DonorEligibilityController::class, 'index'])->name('donor.eligibility');

    Route::get('/donations', [DonationController::class, 'index'])->name('donations.index');

    Route::get('/contact', [FeedbackController::class, 'index'])->name('contact.index');
    Route::post('/contact', [FeedbackController::class, 'store'])->name('contact.store');
});

/*
|--------------------------------------------------------------------------
| PROVIDER (DOCTOR) ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [DoctorDashboardController::class, 'appointments'])->name('appointments');
    
    Route::get('/users', [DoctorDashboardController::class, 'users'])->name('users.index');
    Route::get('/users/create', [DoctorDashboardController::class, 'createUser'])->name('users.create');
    Route::post('/users', [DoctorDashboardController::class, 'storeUser'])->name('users.store');
    
    Route::match(['PUT', 'PATCH'], '/users/{id}', [DoctorDashboardController::class, 'updateUser'])->name('users.update');
    
    Route::get('/appointments/{id}/examine', [DoctorDashboardController::class, 'examine'])->name('appointments.examine');
    Route::post('/appointments/{id}/examine', [DoctorDashboardController::class, 'storeDonation'])->name('appointments.storeDonation');
    
    Route::match(['POST', 'PATCH'], '/appointments/{id}/status', [DoctorDashboardController::class, 'updateStatus'])->name('appointments.status');
    
    Route::get('/appointments/{id}/view', [DoctorDashboardController::class, 'viewResults'])->name('appointments.viewResults');
    Route::get('/appointments/{id}/download', [DoctorDashboardController::class, 'downloadResults'])->name('appointments.download');

    Route::get('/donations', [DoctorDashboardController::class, 'donations'])->name('donations');
    Route::get('/notifications', [DoctorDashboardController::class, 'notifications'])->name('notifications');
    Route::get('/feedback', [DoctorDashboardController::class, 'feedback'])->name('feedback');
    Route::get('/reports', [DoctorDashboardController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [DoctorDashboardController::class, 'exportExcel'])->name('reports.export');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users Management
    Route::get('/users',                 [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/users/create',          [AdminDashboardController::class, 'create'])->name('users.create');
    Route::post('/users',                [AdminDashboardController::class, 'store'])->name('users.store');
    Route::match(['PUT', 'PATCH'], '/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{id}/toggle',   [AdminDashboardController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::delete('/users/{id}',         [AdminDashboardController::class, 'destroyUser'])->name('users.destroy'); // Existing logic
    Route::patch('/users/{id}/role',    [AdminDashboardController::class, 'updateRole'])->name('users.updateRole');
    
    // Integrated Reset Password Route
    Route::post('/users/{id}/reset-password', [AdminDashboardController::class, 'resetPassword'])->name('users.reset-password');

    // Doctors (Providers)
    Route::get('/doctors',               [AdminDashboardController::class, 'doctorsIndex'])->name('doctors.index');
    Route::patch('/doctors/{id}',        [AdminDashboardController::class, 'updateDoctor'])->name('doctors.update');
    Route::post('/doctors/{id}/toggle',  [AdminDashboardController::class, 'toggleUserStatus'])->name('doctors.toggle');
    Route::delete('/doctors/{id}',       [AdminDashboardController::class, 'destroyUser'])->name('doctors.destroy');

    // Appointments Management
    Route::get('/appointments',               [AdminDashboardController::class, 'appointmentsIndex'])->name('appointments.index');
    Route::patch('/appointments/{id}/update', [AdminDashboardController::class, 'updateAppointmentStatus'])->name('appointments.update');
    Route::match(['POST', 'PATCH'], '/appointments/{id}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::get('/appointments/{id}/view', [AdminDashboardController::class, 'viewReport'])->name('appointments.view');

    // Donations overview
    Route::get('/donations', [AdminDashboardController::class, 'donations'])->name('donations');

    // Blood Centers (Locations)
    Route::get('/centers',           [LocationController::class, 'adminIndex'])->name('centers.index');
    Route::get('/centers/create',    [LocationController::class, 'create'])->name('centers.create');
    Route::post('/centers',          [LocationController::class, 'store'])->name('centers.store');
    Route::get('/centers/{id}/edit', [LocationController::class, 'edit'])->name('centers.edit');
    Route::put('/centers/{id}',      [LocationController::class, 'adminUpdate'])->name('centers.update');
    Route::delete('/centers/{id}',   [LocationController::class, 'destroy'])->name('centers.destroy');

    // Feedback
    Route::get('/feedback',             [AdminDashboardController::class, 'feedbackIndex'])->name('feedback.index');
    Route::post('/feedback/{id}/reply', [AdminDashboardController::class, 'replyToFeedback'])->name('feedback.reply');

    // Reports
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
});
