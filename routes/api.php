<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\InvitationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])
    ->name('invitations.accept');

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/user', function (Request $request) {
        return $request->user()->load('company');
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('companies', CompanyController::class)->names([
            'index'   => 'api.companies.index',
            'store'   => 'api.companies.store',
            'show'    => 'api.companies.show',
            'update'  => 'api.companies.update',
            'destroy' => 'api.companies.destroy',
        ]);
        Route::post('/invitations', [InvitationController::class, 'send'])
        ->name('api.invitations.send');
    });

    // Company members
    Route::get('/invitations', [InvitationController::class, 'index'])
        ->name('invitations.index');
});