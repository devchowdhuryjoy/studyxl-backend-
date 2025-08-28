<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\Agent\AuthenticatedSessionController;


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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Admin
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');

   // Agent management
    Route::get('approve-agent/{id}', [AdminController::class, 'approveAgent'])->name('admin.approve.agent');
    Route::get('deactivate-agent/{id}', [AdminController::class, 'deactivateAgent'])->name('admin.deactivate.agent');
    Route::get('activate-agent/{id}', [AdminController::class, 'activateAgent'])->name('admin.activate.agent');
});

Route::prefix('admin')->group(function () {
       Route::get('/login',[AdminController::class,'login'])->name('admin.login');
       Route::post('/login_submit',[AdminController::class,'login_submit'])->name('admin.login_submit');
       Route::get('/logout',[AdminController::class,'logout'])->name('admin.logout');

       Route::get('/forget_password',[AdminController::class,'forget_password'])->name('admin.forget_password');
       Route::post('/forget_password_submit',[AdminController::class,'forget_password_submit'])->name('admin.forget_password_submit');
       Route::post('/forget_password_submit',[AdminController::class,'forget_password_submit'])->name('admin.forget_password_submit');
       Route::get('/reset_password/{token}/{email}', [AdminController::class, 'reset_password'])->name('admin.reset_password');
       Route::post('/reset_password_submit',[AdminController::class,'reset_password_submit'])->name('admin.reset_password_submit');


});

// Agent
Route::middleware(['agent', 'agent.approved'])->prefix('agent')->group(function () {
    Route::get('dashboard', [AgentController::class,'dashboard'])->name('agent.dashboard');
    Route::get('logout', [AgentController::class,'logout'])->name('agent.logout');
});
Route::middleware('guest:agent')->group(function () {
    Route::get('agent/register', [AgentController::class, 'create'])->name('agent.register');
    Route::post('agent/register', [AgentController::class, 'store']);
    Route::get('agent/login', [AuthenticatedSessionController::class, 'create'])->name('agent.login');
    Route::post('agent/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/agent/forget_password',[AgentController::class,'forget_password'])->name('agent.forget_password');
    Route::post('/agent/forget_password_submit',[AgentController::class,'forget_password_submit'])->name('agent.forget_password_submit');
           
    Route::get('/agent/reset_password/{token}/{email}', [AgentController::class, 'reset_password'])->name('agent.reset_password');
    Route::post('/agent/reset_password_submit',[AgentController::class,'reset_password_submit'])->name('agent.reset_password_submit');

     

});






