<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BkLearnController;
use App\Http\Controllers\BkManageController;
use App\Http\Controllers\BkProfileController;
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
    Route::get('/manage', [BkManageController::class, 'index'])->name('manage');
    Route::post('/manage/categories', [BkManageController::class, 'storeCategory'])->name('manage.categories.store');
    Route::put('/manage/categories/{id}', [BkManageController::class, 'updateCategory'])->name('manage.categories.update');
    Route::post('/manage/categories/{id}/toggle', [BkManageController::class, 'toggleCategory'])->name('manage.categories.toggle');
    Route::delete('/manage/categories/{id}', [BkManageController::class, 'destroyCategory'])->name('manage.categories.destroy');
    Route::post('/manage/categories/reorder', [BkManageController::class, 'reorderCategories'])->name('manage.categories.reorder');

    Route::post('/manage/locations', [BkManageController::class, 'storeLocation'])->name('manage.locations.store');
    Route::put('/manage/locations/{id}', [BkManageController::class, 'updateLocation'])->name('manage.locations.update');
    Route::post('/manage/locations/{id}/toggle', [BkManageController::class, 'toggleLocation'])->name('manage.locations.toggle');
    Route::delete('/manage/locations/{id}', [BkManageController::class, 'destroyLocation'])->name('manage.locations.destroy');
    Route::post('/manage/locations/reorder', [BkManageController::class, 'reorderLocations'])->name('manage.locations.reorder');

    Route::post('/manage/staff', [BkManageController::class, 'storeStaff'])->name('manage.staff.store');
    Route::put('/manage/staff/{id}', [BkManageController::class, 'updateStaff'])->name('manage.staff.update');
    Route::delete('/manage/staff/{id}', [BkManageController::class, 'destroyStaff'])->name('manage.staff.destroy');
    Route::post('/manage/staff/{id}/resend', [BkManageController::class, 'resendStaffInvite'])->name('manage.staff.resend');

    Route::get('/manage/poster', [BkManageController::class, 'downloadPoster'])->name('manage.poster');
    Route::get('/manage/qr/stats', [BkManageController::class, 'getQrStats'])->name('manage.qr.stats');
    Route::get('/learn', [BkLearnController::class, 'index'])->name('learn');
    Route::post('/learn', [BkLearnController::class, 'store'])->name('learn.store');
    Route::put('/learn/{id}', [BkLearnController::class, 'update'])->name('learn.update');
    Route::post('/learn/{id}/toggle', [BkLearnController::class, 'toggleVisibility'])->name('learn.toggle');
    Route::delete('/learn/{id}', [BkLearnController::class, 'destroy'])->name('learn.destroy');
    Route::view('/statistics', 'bk.statistics')->name('statistics');
    Route::view('/archive', 'bk.archive')->name('archive');
    Route::view('/access-log', 'bk.access-log')->name('access-log');
    Route::get('/profile', [BkProfileController::class, 'show'])->name('profile');
    Route::post('/profile/update', [BkProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/complete', [BkProfileController::class, 'completeProfile'])->name('profile.complete');
    Route::post('/profile/period', [BkProfileController::class, 'updatePeriod'])->name('profile.period');
    Route::post('/profile/password', [BkProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/logout-all', [BkProfileController::class, 'logoutAllDevices'])->name('profile.logout-all');
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

Route::get('/s/qr', [BkManageController::class, 'handleQrScan'])->name('public.qr');
Route::get('/s/lapor', [BkManageController::class, 'showPublicReport'])->name('public.report');
Route::get('/s/{code}/pelajari', [BkLearnController::class, 'showPublic'])->name('public.learn');
Route::get('/s/{code}/qr', [BkManageController::class, 'handleQrScan']);
Route::get('/s/{code}/lapor', [BkManageController::class, 'showPublicReport']);

// Wali Kelas Portal
Route::prefix('wali-kelas')->name('walas.')->controller(WaliKelasPreviewController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/profile', 'profile')->name('profile');
    Route::get('/ringkasan/{summary}', 'summary')->whereNumber('summary')->name('summary');
});
