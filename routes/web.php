<?php

use App\Http\Controllers\FrontendPreviewController;
use App\Http\Controllers\KesiswaanPreviewController;
use App\Http\Controllers\WaliKelasPreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('/login', 'auth.login')->name('login');
Route::view('/signup', 'auth.signup')->name('register');
Route::view('/two-factor/setup', 'auth.setup-two-factor')->name('two-factor.setup');
Route::view('/two-factor/verify', 'auth.verify-two-factor')->name('two-factor.challenge');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::view('/onboarding/role', 'auth.role')->name('onboarding.role');
Route::view('/onboarding/school', 'auth.school')->name('onboarding.school');

// These routes render source-design samples only; no authenticated records are loaded.
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

Route::post('/frontend/{page}', FrontendPreviewController::class)
    ->whereIn('page', ['login', 'signup', 'verify-two-factor', 'forgot-password', 'school', 'bk', 'kesiswaan', 'walas'])
    ->name('frontend.unavailable');

Route::prefix('kesiswaan')->name('kesiswaan.')->controller(KesiswaanPreviewController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/reports/{report}', 'report')->whereNumber('report')->name('report');
    Route::get('/archive', 'archive')->name('archive');
    Route::get('/statistics', 'statistics')->name('statistics');
    Route::get('/access-log', 'accessLog')->name('access-log');
    Route::get('/profile', 'profile')->name('profile');
});

Route::get('/s/{code}/pelajari', [KesiswaanPreviewController::class, 'publicLearn'])->name('public.learn');

Route::prefix('wali-kelas')->name('walas.')->controller(WaliKelasPreviewController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/profile', 'profile')->name('profile');
    Route::get('/ringkasan/{summary}', 'summary')->whereNumber('summary')->name('summary');
});
