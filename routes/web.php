<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\CompanyUserController;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Companies
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    Route::post('/invitations', [InvitationController::class, 'send'])->name('invitations.send');
    Route::get('/companies/{company}/users', [CompanyUserController::class, 'index'])->name('companies.users');
});

Route::get('/accept-invitation/{token}', [AcceptInvitationController::class, 'show'])
    ->name('invitation.accept.show');
Route::post('/accept-invitation/{token}', [AcceptInvitationController::class, 'accept'])
    ->name('invitation.accept');
Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
