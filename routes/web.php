<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FixedDepositController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SeniorFinanceManagerOnly;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

// ---------- Public: login, sign up, forgot password ----------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/signup', [AuthController::class, 'showSignUp'])->name('signup');
Route::post('/signup', [AuthController::class, 'signUp'])->name('signup.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendCode'])->name('password.forgot.submit');
Route::get('/verify-code', [PasswordResetController::class, 'showVerify'])->name('password.verify');
Route::post('/verify-code', [PasswordResetController::class, 'verifyCode'])->name('password.verify.submit');
Route::get('/reset-password', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset.submit');

// ---------- Logged-in staff ----------
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Fixed deposits
    Route::get('/fd', [FixedDepositController::class, 'index'])->name('fd.list');
    Route::get('/fd/create', [FixedDepositController::class, 'create'])->name('fd.create');
    Route::post('/fd/create', [FixedDepositController::class, 'storeDraft'])->name('fd.create.submit');
    Route::get('/fd/application', [FixedDepositController::class, 'application'])->name('fd.application');
    Route::post('/fd/submit', [FixedDepositController::class, 'submit'])->name('fd.submit');
    Route::get('/fd/view', [FixedDepositController::class, 'show'])->name('fd.view');
    Route::get('/fd/view/application', [FixedDepositController::class, 'applicationView'])->name('fd.application.view');
    Route::get('/fd/certificate', [FixedDepositController::class, 'certificate'])->name('fd.certificate');
    Route::get('/fd/update', [FixedDepositController::class, 'edit'])->name('fd.update');
    Route::post('/fd/update', [FixedDepositController::class, 'update'])->name('fd.update.submit');
    Route::get('/fd/report', [FixedDepositController::class, 'report'])->name('fd.report');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile-picture', [ProfileController::class, 'picture'])->name('profile.picture');

    // Senior Finance Manager only: banks and users
    Route::middleware(SeniorFinanceManagerOnly::class)->group(function () {
        Route::get('/banks', [BankController::class, 'index'])->name('banks.list');
        Route::get('/banks/create', [BankController::class, 'create'])->name('banks.create');
        Route::post('/banks', [BankController::class, 'store'])->name('banks.store');
        Route::get('/banks/edit', [BankController::class, 'edit'])->name('banks.edit');
        Route::post('/banks/update', [BankController::class, 'update'])->name('banks.update');

        Route::get('/users', [UserController::class, 'index'])->name('users.list');
        Route::post('/users/update', [UserController::class, 'update'])->name('users.update');
    });
});
