<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserPermissionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AppPermissionController;
use App\Http\Controllers\CompanySwitchController;

// Public routes
Route::get('/', fn() => redirect()->route('login'));

Route::get('/accept-invitation/{token}', [AcceptInvitationController::class, 'show'])->name('invitation.accept.show');
Route::post('/accept-invitation/{token}', [AcceptInvitationController::class, 'accept'])->name('invitation.accept');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/select-company', [CompanySwitchController::class, 'select'])->name('company.select');
    Route::post('/switch-company', [CompanySwitchController::class, 'switch'])->name('company.switch');
});
// Authenticated routes
Route::middleware('auth', 'active.company')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Admin only
    Route::middleware(['role:admin'])->group(function () {

        // Companies
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
        Route::get('/companies/{company}/users', [CompanyUserController::class, 'index'])->name('companies.users');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/{user}/companies', [CompanyUserController::class, 'userCompanies'])->name('user.companies');
        Route::get('/users/{user}/permissions', [UserPermissionController::class, 'show'])->name('users.permissions');
        Route::put('/users/{user}/permissions', [UserPermissionController::class, 'update'])->name('users.permissions.update');

        // Invitations
        Route::post('/invitations', [InvitationController::class, 'send'])->name('invitations.send');
        Route::delete('/invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');

        // Permissions management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // Audit
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');

        // Export
        Route::get('/export/companies', [ExportController::class, 'companies'])->name('export.companies');
        Route::get('/export/users', [ExportController::class, 'users'])->name('export.users');

        // Apps management
        Route::get('/apps', [AppController::class, 'index'])->name('apps.index');
        Route::post('/apps', [AppController::class, 'store'])->name('apps.store');
        Route::patch('/apps/{app}/toggle', [AppController::class, 'toggle'])->name('apps.toggle');
        Route::delete('/apps/{app}', [AppController::class, 'destroy'])->name('apps.destroy');

        // Company app assignment
        Route::get('/companies/{company}/apps', [AppController::class, 'companyApps'])->name('apps.company');
        Route::put('/companies/{company}/apps', [AppController::class, 'syncCompanyApps'])->name('apps.company.sync');

        // User app assignment per company
        Route::get('/users/{user}/companies/{company}/apps', [AppController::class, 'userApps'])->name('apps.user');
        Route::put('/users/{user}/companies/{company}/apps', [AppController::class, 'syncUserApps'])->name('apps.user.sync');

        // User permissions per app+company
        Route::get('/users/{user}/companies/{company}/apps/{app}/permissions', [AppPermissionController::class, 'show'])->name('app.permissions.show');
        Route::put('/users/{user}/companies/{company}/apps/{app}/permissions', [AppPermissionController::class, 'update'])->name('app.permissions.update');
    });

});
