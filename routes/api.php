<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Api\Parent\StudentController as ParentStudentController;
use App\Http\Controllers\Api\Parent\AttendanceController as ParentAttendanceController;
use App\Http\Controllers\Api\Parent\ProgressController as ParentProgressController;
use App\Http\Controllers\Api\Parent\MediaController as ParentMediaController;
use App\Http\Controllers\Api\Parent\PaymentController as ParentPaymentController;
use App\Http\Controllers\Api\Parent\InvoiceController as ParentInvoiceController;
use App\Http\Controllers\Api\Parent\MessageController as ParentMessageController;
use App\Http\Controllers\Api\Parent\ProfileController as ParentProfileController;
use App\Http\Controllers\Api\Parent\MakeupSessionController as ParentMakeupSessionController;
use App\Http\Controllers\Api\Parent\MenuController as ParentMenuController;
use App\Http\Controllers\Api\Parent\CalendarController as ParentCalendarController;
use App\Http\Controllers\Api\Parent\NotificationController as ParentNotificationController;
use App\Http\Controllers\Api\Coach\DashboardController as CoachDashboardController;
use App\Http\Controllers\Api\Coach\ClassController as CoachClassController;
use App\Http\Controllers\Api\Coach\AttendanceController as CoachAttendanceController;
use App\Http\Controllers\Api\Coach\MediaController as CoachMediaController;
use App\Http\Controllers\Api\Coach\MessageController as CoachMessageController;
use App\Http\Controllers\Api\Coach\ProfileController as CoachProfileController;
use App\Http\Controllers\Api\Coach\MakeupSessionController as CoachMakeupSessionController;
use App\Http\Controllers\Api\Coach\NotificationController as CoachNotificationController;
use App\Http\Controllers\Api\Coach\StudentProgressController as CoachStudentProgressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobil API Routes (Veli + Antrenör)
|--------------------------------------------------------------------------
|
| Bearer token ile kimlik doğrulama (Laravel Sanctum).
| POST /api/login -> email, password -> token alır.
| Tüm isteklerde: Authorization: Bearer {token}
|
*/

// Auth (login token döner)
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Token ile korumalı route'lar (veli veya antrenör)
Route::middleware(['auth:sanctum', 'role:parent|coach', 'check.school.license.api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
});

// Veli API
Route::prefix('parent')
    ->middleware(['auth:sanctum', 'role:parent', 'check.school.license.api'])
    ->name('api.parent.')
    ->group(function () {
        Route::get('/menu', [ParentMenuController::class, 'index']);
        Route::get('/dashboard', [ParentDashboardController::class, 'show']);
        Route::get('/calendar', [ParentCalendarController::class, 'index']);
        Route::get('/students', [ParentStudentController::class, 'index']);
        Route::get('/attendances', [ParentAttendanceController::class, 'index']);
        Route::get('/progress', [ParentProgressController::class, 'index']);
        Route::get('/progress/{progress}', [ParentProgressController::class, 'show']);
        Route::get('/media', [ParentMediaController::class, 'index']);
        Route::get('/media/{id}', [ParentMediaController::class, 'show']);
        Route::get('/media/{id}/file', [ParentMediaController::class, 'file']);
        Route::get('/payments', [ParentPaymentController::class, 'pendingFees']);
        Route::get('/payments/history', [ParentPaymentController::class, 'history']);
        Route::post('/payments/{studentFee}/pay', [ParentPaymentController::class, 'pay']);
        Route::get('/invoices', [ParentInvoiceController::class, 'index']);
        Route::get('/messages', [ParentMessageController::class, 'index']);
        Route::get('/messages/{conversation}', [ParentMessageController::class, 'show']);
        Route::post('/messages', [ParentMessageController::class, 'store']);
        Route::post('/messages/{conversation}/reply', [ParentMessageController::class, 'reply']);
        Route::get('/profile', [ParentProfileController::class, 'show']);
        Route::put('/profile', [ParentProfileController::class, 'update']);
        Route::get('/makeup-sessions', [ParentMakeupSessionController::class, 'index']);
        Route::get('/notifications', [ParentNotificationController::class, 'index']);
        Route::post('/notifications/read-all', [ParentNotificationController::class, 'markAllAsRead']);
        Route::post('/notifications/{id}/read', [ParentNotificationController::class, 'markAsRead']);
    });

// Antrenör API
Route::prefix('coach')
    ->middleware(['auth:sanctum', 'role:coach', 'check.school.license.api'])
    ->name('api.coach.')
    ->group(function () {
        Route::get('/dashboard', [CoachDashboardController::class, 'show']);
        Route::get('/classes', [CoachClassController::class, 'index']);
        Route::get('/classes/{id}', [CoachClassController::class, 'show']);
        Route::get('/classes/{id}/students', [CoachClassController::class, 'students']);
        Route::get('/attendances', [CoachAttendanceController::class, 'index']);
        Route::get('/attendances/today-classes', [CoachAttendanceController::class, 'todayClasses']);
        Route::get('/attendances/form-students', [CoachAttendanceController::class, 'formStudents']);
        Route::post('/attendances', [CoachAttendanceController::class, 'store']);
        Route::get('/media', [CoachMediaController::class, 'index']);
        Route::get('/media/{id}', [CoachMediaController::class, 'show']);
        Route::get('/media/{id}/file', [CoachMediaController::class, 'file']);
        Route::get('/messages', [CoachMessageController::class, 'index']);
        Route::get('/messages/{conversation}', [CoachMessageController::class, 'show']);
        Route::post('/messages/{conversation}/reply', [CoachMessageController::class, 'reply']);
        Route::get('/profile', [CoachProfileController::class, 'show']);
        Route::put('/profile', [CoachProfileController::class, 'update']);
        Route::get('/makeup-sessions', [CoachMakeupSessionController::class, 'index']);
        Route::get('/makeup-sessions/{id}', [CoachMakeupSessionController::class, 'show']);
        Route::get('/notifications', [CoachNotificationController::class, 'index']);
        Route::post('/notifications/read-all', [CoachNotificationController::class, 'markAllAsRead']);
        Route::post('/notifications/{id}/read', [CoachNotificationController::class, 'markAsRead']);
        Route::get('/student-progress', [CoachStudentProgressController::class, 'index']);
        Route::post('/student-progress', [CoachStudentProgressController::class, 'store']);
    });
