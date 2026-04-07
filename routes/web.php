<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/yardim', [\App\Http\Controllers\PageController::class, 'help'])->name('help');
Route::get('/iletisim', [\App\Http\Controllers\PageController::class, 'contact'])->name('contact');
Route::get('/sss', [\App\Http\Controllers\PageController::class, 'faq'])->name('faq');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/sifremi-unuttum', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/sifremi-unuttum', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/sifre-sifirla/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/sifre-sifirla', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Lisans süresi doldu (admin/coach/parent yönlendirilir)
Route::middleware(['auth'])->get('/license-expired', \App\Http\Controllers\LicenseExpiredController::class)->name('license-expired');

// Superadmin routes
Route::middleware(['auth'])->middleware('role:superadmin')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Superadmin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('schools-expired', [\App\Http\Controllers\Superadmin\SchoolController::class, 'expired'])->name('schools.expired');
    Route::post('schools/{school}/extend-license', [\App\Http\Controllers\Superadmin\SchoolController::class, 'extendLicense'])->name('schools.extend-license');
    Route::resource('schools', \App\Http\Controllers\Superadmin\SchoolController::class);
    Route::resource('applications', \App\Http\Controllers\Superadmin\ApplicationController::class);
    Route::post('/applications/{application}/approve', [\App\Http\Controllers\Superadmin\ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject', [\App\Http\Controllers\Superadmin\ApplicationController::class, 'reject'])->name('applications.reject');
    Route::resource('payments', \App\Http\Controllers\Superadmin\PaymentController::class)->only(['index', 'show']);
    Route::resource('distributions', \App\Http\Controllers\Superadmin\DistributionController::class)->only(['index', 'show']);
    Route::get('/commission', [\App\Http\Controllers\Superadmin\CommissionController::class, 'index'])->name('commission.index');
    Route::put('/commission/{school}', [\App\Http\Controllers\Superadmin\CommissionController::class, 'update'])->name('commission.update');
    Route::get('/reports', [\App\Http\Controllers\Superadmin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/analytics', [\App\Http\Controllers\Superadmin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::resource('users', \App\Http\Controllers\Superadmin\UserController::class)->only(['index', 'show']);
    Route::get('/settings', [\App\Http\Controllers\Superadmin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Superadmin\SettingController::class, 'update'])->name('settings.update');
    Route::get('/payment-settings', [\App\Http\Controllers\Superadmin\PaymentSettingController::class, 'index'])->name('payment-settings.index');
    Route::put('/payment-settings', [\App\Http\Controllers\Superadmin\PaymentSettingController::class, 'update'])->name('payment-settings.update');
    Route::post('/payment-settings/test-connection', [\App\Http\Controllers\Superadmin\PaymentSettingController::class, 'testConnection'])->name('payment-settings.test-connection');
    Route::get('/security', [\App\Http\Controllers\Superadmin\SecurityController::class, 'index'])->name('security.index');
    Route::put('/security', [\App\Http\Controllers\Superadmin\SecurityController::class, 'update'])->name('security.update');
    Route::get('/footer-settings', [\App\Http\Controllers\Superadmin\FooterSettingController::class, 'index'])->name('footer-settings.index');
    Route::put('/footer-settings', [\App\Http\Controllers\Superadmin\FooterSettingController::class, 'update'])->name('footer-settings.update');
    Route::get('/netgsm-settings', [\App\Http\Controllers\Superadmin\NetGsmSettingController::class, 'index'])->name('netgsm-settings.index');
    Route::post('/netgsm-settings/test-send', [\App\Http\Controllers\Superadmin\NetGsmSettingController::class, 'testSend'])->name('netgsm-settings.test-send');
    Route::put('/netgsm-settings', [\App\Http\Controllers\Superadmin\NetGsmSettingController::class, 'update'])->name('netgsm-settings.update');
});

// Admin routes
Route::middleware(['auth', 'role:admin', 'check.school.license'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/students/find-by-tc', [\App\Http\Controllers\Admin\StudentController::class, 'findByTc'])->name('students.find-by-tc');
    Route::post('/students/{student}/aidat', [\App\Http\Controllers\Admin\StudentController::class, 'storeAidat'])->name('students.store-aidat');
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::resource('coaches', \App\Http\Controllers\Admin\CoachController::class);
    Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class);
    Route::resource('sport-branches', \App\Http\Controllers\Admin\SportBranchController::class);
    Route::get('/classes/{class}/students-to-add', [\App\Http\Controllers\Admin\ClassController::class, 'studentsToAdd'])->name('classes.students-to-add');
    Route::post('/classes/{class}/assign-students', [\App\Http\Controllers\Admin\ClassController::class, 'assignStudents'])->name('classes.assign-students');
    Route::resource('classes', \App\Http\Controllers\Admin\ClassController::class);
    Route::resource('parents', \App\Http\Controllers\Admin\ParentController::class);
    Route::resource('fee-plans', \App\Http\Controllers\Admin\FeePlanController::class);
    Route::resource('student-fees', \App\Http\Controllers\Admin\StudentFeeController::class);
    Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class)->only(['index', 'show']);
    Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
    Route::get('/media/create', [\App\Http\Controllers\Admin\MediaController::class, 'create'])->name('media.create');
    Route::post('/media', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.store');
    Route::get('/media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'show'])->name('media.show');
    Route::delete('/media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/attendances', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
    Route::resource('bank-accounts', \App\Http\Controllers\Admin\BankAccountController::class);
    Route::post('/bank-accounts/{bankAccount}/create-submerchant', [\App\Http\Controllers\Admin\BankAccountController::class, 'createSubMerchant'])->name('bank-accounts.create-submerchant');
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/school-settings', [\App\Http\Controllers\Admin\SchoolSettingController::class, 'index'])->name('school-settings.index');
    Route::put('/school-settings', [\App\Http\Controllers\Admin\SchoolSettingController::class, 'update'])->name('school-settings.update');
    Route::get('/user-settings', [\App\Http\Controllers\Admin\UserSettingController::class, 'index'])->name('user-settings.index');
    Route::put('/user-settings', [\App\Http\Controllers\Admin\UserSettingController::class, 'update'])->name('user-settings.update');
    
    // Telafi Dersleri (sadece telafi dersi veriliyorsa görünür)
    // Spesifik route'lar resource route'dan önce tanımlanmalı
    Route::get('/class-cancellations/calendar-events-week', [\App\Http\Controllers\Admin\ClassCancellationController::class, 'getCalendarEventsForWeek'])->name('class-cancellations.calendar-events-week');
    Route::get('/class-cancellations/calendar-events', [\App\Http\Controllers\Admin\ClassCancellationController::class, 'getCalendarEventsForDate'])->name('class-cancellations.calendar-events');
    Route::get('/class-cancellations/{classCancellation}/stats', [\App\Http\Controllers\Admin\ClassCancellationController::class, 'cancellationStats'])->name('class-cancellations.stats');
    Route::get('/class-cancellations/{classCancellation}/add-makeup', [\App\Http\Controllers\Admin\ClassCancellationController::class, 'addMakeupForm'])->name('class-cancellations.add-makeup');
    Route::get('/class-cancellations/{classCancellation}/waiting-students', [\App\Http\Controllers\Admin\ClassCancellationController::class, 'waitingStudents'])->name('class-cancellations.waiting-students');
    Route::get('/class-cancellations/check-conflict', [\App\Http\Controllers\Admin\ClassCancellationController::class, 'checkScheduleConflict'])->name('class-cancellations.check-conflict');
    Route::post('/class-cancellations/{classCancellation}/store-makeup', [\App\Http\Controllers\Admin\ClassCancellationController::class, 'storeMakeupFromCancellation'])->name('class-cancellations.store-makeup');
    Route::resource('class-cancellations', \App\Http\Controllers\Admin\ClassCancellationController::class);
    Route::get('/student-makeup-classes', [\App\Http\Controllers\Admin\StudentMakeupClassController::class, 'index'])->name('student-makeup-classes.index');
    Route::put('/student-makeup-classes/{id}', [\App\Http\Controllers\Admin\StudentMakeupClassController::class, 'update'])->name('student-makeup-classes.update');
    Route::get('/student-makeup-classes/classes-by-date', [\App\Http\Controllers\Admin\StudentMakeupClassController::class, 'getClassesByDate'])->name('student-makeup-classes.classes-by-date');

    Route::get('/makeup-sessions/pending-students', [\App\Http\Controllers\Admin\MakeupSessionController::class, 'pendingStudents'])->name('makeup-sessions.pending-students');
    Route::post('/makeup-sessions/{makeupSession}/add-students', [\App\Http\Controllers\Admin\MakeupSessionController::class, 'addStudents'])->name('makeup-sessions.add-students');
    Route::resource('makeup-sessions', \App\Http\Controllers\Admin\MakeupSessionController::class)->names('makeup-sessions');
});

// Coach routes
Route::middleware(['auth', 'role:coach', 'check.school.license'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Coach\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/attendances', [\App\Http\Controllers\Coach\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/create', [\App\Http\Controllers\Coach\AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/attendances', [\App\Http\Controllers\Coach\AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/media', [\App\Http\Controllers\Coach\MediaController::class, 'index'])->name('media.index');
    Route::get('/media/create', [\App\Http\Controllers\Coach\MediaController::class, 'create'])->name('media.create');
    Route::post('/media', [\App\Http\Controllers\Coach\MediaController::class, 'store'])->name('media.store');
    Route::get('/media/{id}', [\App\Http\Controllers\Coach\MediaController::class, 'show'])->name('media.show');
    Route::delete('/media/{id}', [\App\Http\Controllers\Coach\MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/classes', [\App\Http\Controllers\Coach\ClassController::class, 'index'])->name('classes.index');
    Route::resource('student-progress', \App\Http\Controllers\Coach\StudentProgressController::class)->except(['show']);
    Route::get('/reports', [\App\Http\Controllers\Coach\ReportController::class, 'index'])->name('reports.index');
    Route::get('/profile', [\App\Http\Controllers\Coach\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Coach\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/makeup-sessions', [\App\Http\Controllers\Coach\MakeupSessionController::class, 'index'])->name('makeup-sessions.index');
    Route::get('/messages', [\App\Http\Controllers\Coach\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [\App\Http\Controllers\Coach\MessageController::class, 'show'])->name('messages.show')->where('message', '[0-9]+');
    Route::post('/messages/{message}/reply', [\App\Http\Controllers\Coach\MessageController::class, 'reply'])->name('messages.reply')->where('message', '[0-9]+');
});

// Parent routes
Route::middleware(['auth', 'role:parent', 'check.school.license'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/media', [\App\Http\Controllers\Parent\MediaController::class, 'index'])->name('media.index');
    Route::get('/media/{id}', [\App\Http\Controllers\Parent\MediaController::class, 'show'])->name('media.show');
    Route::get('/payments', [\App\Http\Controllers\Parent\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/history', [\App\Http\Controllers\Parent\PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{studentFee}/create', [\App\Http\Controllers\Parent\PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{studentFee}', [\App\Http\Controllers\Parent\PaymentController::class, 'store'])->name('payments.store');
    Route::get('/student', [\App\Http\Controllers\Parent\StudentController::class, 'index'])->name('student.index');
    Route::get('/attendances', [\App\Http\Controllers\Parent\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/progress', [\App\Http\Controllers\Parent\ProgressController::class, 'index'])->name('progress.index');
    Route::get('/progress/{progress}', [\App\Http\Controllers\Parent\ProgressController::class, 'show'])->name('progress.show');
    Route::get('/invoices', [\App\Http\Controllers\Parent\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/messages', [\App\Http\Controllers\Parent\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [\App\Http\Controllers\Parent\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [\App\Http\Controllers\Parent\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [\App\Http\Controllers\Parent\MessageController::class, 'show'])->name('messages.show')->where('message', '[0-9]+');
    Route::post('/messages/{message}/reply', [\App\Http\Controllers\Parent\MessageController::class, 'reply'])->name('messages.reply')->where('message', '[0-9]+');
    Route::get('/profile', [\App\Http\Controllers\Parent\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Parent\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/children', [\App\Http\Controllers\Parent\ChildrenController::class, 'index'])->name('children.index');
    Route::get('/makeup-sessions', [\App\Http\Controllers\Parent\MakeupSessionController::class, 'index'])->name('makeup-sessions.index');
});
