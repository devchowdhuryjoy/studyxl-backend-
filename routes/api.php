<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
// use App\Http\Controllers\Agent\AuthenticatedSessionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

// Student  ApI
Route::middleware('guest')->group(function () {
    // Register
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Login
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Forgot Password
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store']);

    // Reset Password
    Route::post('reset-password', [NewPasswordController::class, 'store']);
});


Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);

    // Email Verification
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke']);
    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Confirm Password
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Update Password
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});

//Agent API
Route::middleware(['agent', 'agent.approved'])->prefix('agent')->group(function () {
    Route::get('dashboard', [AgentController::class,'dashboard'])->name('agent.dashboard');
    Route::get('logout', [AgentController::class,'logout'])->name('agent.logout');
});
Route::middleware('guest:agent')->group(function () {
    Route::post('agent/register', [AgentController::class, 'store']);
    Route::post('agent/login', [App\Http\Controllers\Agent\AuthenticatedSessionController::class, 'store']);
    Route::post('/agent/forget_password_submit',[AgentController::class,'forget_password_submit'])->name('agent.forget_password_submit');     
    Route::get('/agent/reset_password/{token}/{email}', [AgentController::class, 'reset_password'])->name('agent.reset_password');
    Route::post('/agent/reset_password_submit',[AgentController::class,'reset_password_submit'])->name('agent.reset_password_submit');

     

});


// Admin API
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminController::class, 'login_submit'])->name('admin.login');
    Route::post('/forget_password', [AdminController::class, 'forget_password_submit'])->name('admin.forget_password');
    Route::post('/reset_password_submit',[AdminController::class,'reset_password_submit'])->name('admin.reset_password_submit');
    Route::get('/approve-agent/{id}', [AdminController::class, 'approveAgent'])->name('admin.approve.agent');
    Route::get('activate-agent/{id}', [AdminController::class, 'activateAgent'])->name('admin.activate.agent');
    Route::get('/deactivate-agent/{id}', [AdminController::class, 'deactivateAgent'])->name('admin.deactivate.agent');
});

Route::middleware('auth:admin-api')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.api.dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.api.logout');
});



