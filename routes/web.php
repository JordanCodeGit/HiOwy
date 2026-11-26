<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BkLearnController;
use App\Http\Controllers\BkManageController;
use App\Http\Controllers\BkProfileController;
use App\Http\Controllers\BkReportController;
use App\Http\Controllers\KesiswaanPreviewController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\StaffInvitationController;
use App\Http\Controllers\WaliKelasPreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (khusus staf terundang + 2FA)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Signed Staff Invitation Acceptance Flow (M1)
Route::get('/undangan-staf/{staff}', [StaffInvitationController::class, 'show'])
    ->middleware('signed')
    ->name('staff.invitation.show');
Route::post('/undangan-staf/{staff}', [StaffInvitationController::class, 'accept'])
    ->middleware('signed')
    ->name('staff.invitation.accept');

// 2FA (Setup & Challenge)
Route::get('/two-factor', [AuthController::class, 'showTwoFactorSetup'])->name('two-factor.setup');
Route::post('/two-factor', [AuthController::class, 'confirmTwoFactorSetup'])->name('two-factor.setup.confirm');
Route::post('/two-factor/check', [AuthController::class, 'checkTwoFactorCode'])->name('two-factor.check');
Route::get('/two-factor/verify', fn () => redirect()->route('two-factor.setup'))->name('two-factor.challenge');
Route::get('/two-factor/setup', fn () => redirect()->route('two-factor.setup'));
Route::post('/two-factor/verify', [AuthController::class, 'confirmTwoFactorSetup'])->name('two-factor.verify');
Route::get('/two-factor/activate/{user}', [AuthController::class, 'showTwoFactorActivate'])->name('two-factor.activate');

// Password Reset Flow
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Shared Staff Preference Route (BK, Kesiswaan, & Wali Kelas)
Route::post('/staff/academic-year', [BkProfileController::class, 'switchAcademicYear'])
    ->middleware(['auth', '2fa'])
    ->name('staff.academic-year');

// BK Portal (wajib login, 2FA terverifikasi, dan role:bk)
Route::prefix('bk')->name('bk.')->middleware(['auth', '2fa', 'role:bk'])->group(function () {
    Route::get('/', [BkReportController::class, 'dashboard'])->name('dashboard');
    Route::post('/reports/bulk-iseng', [BkReportController::class, 'bulkMarkIseng'])->name('reports.bulk-iseng');
    Route::post('/reports/bulk-unarchive', [BkReportController::class, 'bulkUnarchive'])->name('reports.bulk-unarchive');
    Route::get('/reports/preview', [BkReportController::class, 'preview'])->name('report');
    Route::get('/reports/{id}', [BkReportController::class, 'show'])->whereNumber('id')->name('reports.show');
    Route::post('/reports/{id}/status', [BkReportController::class, 'updateStatus'])->whereNumber('id')->name('reports.status');
    Route::post('/reports/{id}/category', [BkReportController::class, 'updateCategory'])->whereNumber('id')->name('reports.category');
    Route::post('/reports/{id}/reply', [BkReportController::class, 'storeCounselorReply'])->whereNumber('id')->name('reports.reply');
    Route::post('/reports/{id}/note', [BkReportController::class, 'storeInternalNote'])->whereNumber('id')->name('reports.note');
    Route::post('/reports/{id}/forward', [BkReportController::class, 'forwardToWalas'])->whereNumber('id')->name('reports.forward');
    Route::post('/reports/{id}/archive', [BkReportController::class, 'archive'])->whereNumber('id')->name('reports.archive');
    Route::post('/reports/{id}/unarchive', [BkReportController::class, 'unarchive'])->whereNumber('id')->name('reports.unarchive');
    Route::post('/reports/{id}/iseng', [BkReportController::class, 'markIseng'])->whereNumber('id')->name('reports.iseng');

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
    Route::post('/manage/staff/{id}/reset-access', [BkManageController::class, 'resetStaffAccess'])->name('manage.staff.reset');
    Route::delete('/manage/staff/{id}', [BkManageController::class, 'destroyStaff'])->name('manage.staff.destroy');
    Route::post('/manage/staff/{id}/resend', [BkManageController::class, 'resendStaffInvite'])->name('manage.staff.resend');
    Route::post('/manage/settings', [BkManageController::class, 'updateSettings'])->name('manage.settings');
    Route::post('/manage/retention/run', [BkManageController::class, 'runRetention'])->name('manage.retention.run');

    Route::get('/manage/poster', [BkManageController::class, 'downloadPoster'])->name('manage.poster');
    Route::get('/learn', [BkLearnController::class, 'index'])->name('learn');
    Route::post('/learn', [BkLearnController::class, 'store'])->name('learn.store');
    Route::post('/learn/seed-guide', [BkLearnController::class, 'seedOfficialGuide'])->name('learn.seed-guide');
    Route::put('/learn/{id}', [BkLearnController::class, 'update'])->name('learn.update');
    Route::post('/learn/{id}/toggle', [BkLearnController::class, 'toggleVisibility'])->name('learn.toggle');
    Route::delete('/learn/{id}', [BkLearnController::class, 'destroy'])->name('learn.destroy');
    Route::get('/statistics', [BkReportController::class, 'statistics'])->name('statistics');
    Route::get('/statistics/export/csv', [BkReportController::class, 'exportStatisticsCsv'])->name('statistics.csv');
    Route::get('/statistics/export/pdf', [BkReportController::class, 'exportStatisticsPdf'])->name('statistics.pdf');
    Route::get('/archive', [BkReportController::class, 'archiveIndex'])->name('archive');
    Route::get('/access-log', [BkReportController::class, 'accessLog'])->name('access-log');
    Route::get('/profile', [BkProfileController::class, 'show'])->name('profile');
    Route::post('/profile/update', [BkProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/period', [BkProfileController::class, 'updatePeriod'])->name('profile.period');
    Route::post('/profile/password', [BkProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/logout-all', [BkProfileController::class, 'logoutAllDevices'])->name('profile.logout-all');
});

// Kesiswaan Portal (wajib login, 2FA terverifikasi, dan role:kesiswaan)
Route::prefix('kesiswaan')->name('kesiswaan.')->middleware(['auth', '2fa', 'role:kesiswaan'])->controller(KesiswaanPreviewController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::post('/reports/bulk-iseng', 'bulkMarkIseng')->name('reports.bulk-iseng');
    Route::get('/reports/{report}', 'report')->whereNumber('report')->name('report');
    Route::post('/reports/{report}/status', 'updateStatus')->whereNumber('report')->name('reports.status');
    Route::post('/reports/{report}/note', 'storeNote')->whereNumber('report')->name('reports.note');
    Route::post('/reports/{report}/archive', 'archiveReport')->whereNumber('report')->name('reports.archive');
    Route::post('/reports/{report}/unarchive', 'unarchiveReport')->whereNumber('report')->name('reports.unarchive');
    Route::post('/reports/{report}/iseng', 'markIseng')->whereNumber('report')->name('reports.iseng');
    Route::get('/archive', 'archive')->name('archive');
    Route::get('/statistics', 'statistics')->name('statistics');
    Route::get('/access-log', 'accessLog')->name('access-log');
    Route::get('/profile', 'profile')->name('profile');
    Route::post('/profile/update', 'updateProfile')->name('profile.update');
    Route::post('/profile/password', 'updatePassword')->name('profile.password');
});

// Public Reporter & Educational Portal (tanpa login + anti-back-cache FR-66/FR-67)
Route::middleware('public.nocache')->group(function () {
    Route::get('/s/qr', [BkManageController::class, 'handleQrScan'])->name('public.qr');
    Route::get('/s/lapor', [BkManageController::class, 'showPublicReport'])->name('public.report');
    Route::post('/s/lapor', [BkManageController::class, 'storePublicReport'])->name('public.report.store');
    Route::post('/s/lapor/ticket-password', [BkManageController::class, 'setTicketPassword'])->name('public.report.password');
    Route::match(['get', 'post'], '/s/lapor/track', [BkManageController::class, 'trackReport'])->name('public.report.track');
    Route::post('/s/lapor/track/reply', [BkManageController::class, 'replyReport'])->name('public.report.reply');
    Route::get('/latihan', [BkManageController::class, 'showTrainingReport'])->name('public.training');
    Route::post('/latihan', [BkManageController::class, 'storeTrainingReport'])->name('public.training.store');
    Route::get('/s/pelajari', [BkLearnController::class, 'showPublic'])->name('public.learn.default');
    Route::get('/s/{code}/pelajari', [BkLearnController::class, 'showPublic'])->name('public.learn');
    Route::get('/s/{code}/qr', [BkManageController::class, 'handleQrScan']);
    Route::get('/s/{code}/lapor', [BkManageController::class, 'showPublicReport']);
    Route::post('/s/{code}/lapor', [BkManageController::class, 'storePublicReport']);
});

// Wali Kelas Portal (wajib login, 2FA terverifikasi, dan role:walas)
Route::prefix('wali-kelas')->name('walas.')->middleware(['auth', '2fa', 'role:walas'])->controller(WaliKelasPreviewController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/ringkasan/{summary}', 'summary')->whereNumber('summary')->name('summary');
    Route::post('/ringkasan/{summary}/toggle-read', 'toggleRead')->whereNumber('summary')->name('summary.toggle-read');
    Route::post('/ringkasan/mark-all-read', 'markAllRead')->name('summary.mark-all-read');
    Route::get('/ringkasan/{summary}/pdf', 'downloadPdf')->whereNumber('summary')->name('summary.pdf');
    Route::get('/poster', 'downloadPoster')->name('poster');

    Route::get('/profile', 'profile')->name('profile');
    Route::post('/profile/update', 'updateProfile')->name('profile.update');
    Route::post('/profile/password', 'updatePassword')->name('profile.password');
});
