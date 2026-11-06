<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontendPreviewController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\KesiswaanPreviewController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\WaliKelasPreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Sign Up (form web)
Route::get('/signup', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/signup', [RegisterController::class, 'register'])->name('register.attempt');

// 2FA (Halaman tunggal untuk semua alur 2FA)
Route::get('/two-factor', [AuthController::class, 'showTwoFactorSetup'])->name('two-factor.setup');
Route::post('/two-factor', [AuthController::class, 'confirmTwoFactorSetup'])->name('two-factor.setup.confirm');

// Redirect jalur lama ke /two-factor
Route::get('/two-factor/verify', fn () => redirect()->route('two-factor.setup'))->name('two-factor.challenge');
Route::get('/two-factor/setup', fn () => redirect()->route('two-factor.setup'));
Route::post('/two-factor/verify', [AuthController::class, 'confirmTwoFactorSetup'])->name('two-factor.verify');
Route::get('/two-factor/activate/{user}', [AuthController::class, 'showTwoFactorActivate'])->name('two-factor.activate');

// Password Reset Flow
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Onboarding
Route::view('/onboarding/role', 'auth.role')->name('onboarding.role');
Route::view('/onboarding/school', 'auth.school')->name('onboarding.school');

// BK Portal
Route::prefix('bk')->name('bk.')->group(function () {
    Route::view('/', 'bk.dashboard')->name('dashboard');
    Route::view('/manage', 'bk.manage')->name('manage');
    Route::view('/learn', 'bk.learn')->name('learn');
    Route::view('/statistics', 'bk.statistics')->name('statistics');
    Route::view('/archive', 'bk.archive')->name('archive');
    Route::view('/access-log', 'bk.access-log')->name('access-log');
    Route::view('/profile', 'bk.profile')->name('profile');
    Route::view('/reports/preview', 'bk.report')->name('report');
});

// Frontend Preview Fallback
Route::post('/frontend/{page}', FrontendPreviewController::class)
    ->whereIn('page', ['login', 'signup', 'verify-two-factor', 'forgot-password', 'school', 'bk', 'kesiswaan', 'walas'])
    ->name('frontend.unavailable');

// Kesiswaan Portal
Route::prefix('kesiswaan')->name('kesiswaan.')->controller(KesiswaanPreviewController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/reports/{report}', 'report')->whereNumber('report')->name('report');
    Route::get('/archive', 'archive')->name('archive');
    Route::get('/statistics', 'statistics')->name('statistics');
    Route::get('/access-log', 'accessLog')->name('access-log');
    Route::get('/profile', 'profile')->name('profile');
});

Route::get('/s/{code}/pelajari', [KesiswaanPreviewController::class, 'publicLearn'])->name('public.learn');

// Wali Kelas Portal
Route::prefix('wali-kelas')->name('walas.')->controller(WaliKelasPreviewController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/profile', 'profile')->name('profile');
    Route::get('/ringkasan/{summary}', 'summary')->whereNumber('summary')->name('summary');
});
