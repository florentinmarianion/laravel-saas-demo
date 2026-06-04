<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppUserPermission;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

/**
* Manages granular permissions for a user in an app+company.
* Accessible from Cloud Admin (root) or from the individual app.
*
* Routes to add to routes/web.php (in the role:admin group):
* Route::get('/users/{user}/companies/{company}/apps/{app}/permissions',
* [AppPermissionController::class, 'show'])->name('app.permissions.show');
* Route::put('/users/{user}/companies/{company}/apps/{app}/permissions',
* [AppPermissionController::class, 'update'])->name('app.permissions.update');
*/
class AppPermissionController extends Controller
{
    /**
     * Afișează permisiunile userului pentru o app+companie.
     */
    public function show(User $user, Company $company, App $app)
    {
        // Permisiunile disponibile pentru această app
        $availablePermissions = $this->getAvailablePermissions($app->slug);

        // Permisiunile pe care le are deja userul
        $userPermissions = AppUserPermission::where('user_id', $user->id)
            ->where('company_id', $company->id)
            ->where('app_id', $app->id)
            ->where('granted', true)
            ->pluck('permission_key')
            ->toArray();

        return view('apps.permissions', compact(
            'user', 'company', 'app',
            'availablePermissions', 'userPermissions'
        ));
    }

    /**
     * Sincronizează permisiunile userului pentru o app+companie.
     */
    public function update(Request $request, User $user, Company $company, App $app)
    {
        $permissions = $request->input('permissions', []);

        $user->syncAppPermissions($app->id, $company->id, $permissions);

        AuditLog::record('app.permissions.updated', $user, [
            'user'        => $user->name,
            'company'     => $company->name,
            'app'         => $app->name,
            'permissions' => implode(', ', $permissions),
        ]);

        return redirect()
            ->route('app.permissions.show', [$user, $company, $app])
            ->with('success', "Permissions updated for {$user->name} in {$app->name}.");
    }

    /**
     * Returnează permisiunile disponibile per app slug.
     * Extinde această metodă pe măsură ce adaugi module noi.
     *
     * Structura: 'grup' => ['permission_key' => 'Label']
     */
    public function getAvailablePermissions(string $appSlug): array
    {
        $permissions = [
            'currency-exchange' => [
                'General' => [
                    'view'   => 'View exchange rates',
                    'export' => 'Export rates to CSV',
                ],
            ],
            'hr' => [
                'Employees' => [
                    'employees.view'   => 'View employees',
                    'employees.create' => 'Add employees',
                    'employees.update' => 'Edit employees',
                    'employees.delete' => 'Delete employees',
                ],
                'Payroll' => [
                    'salary.view'    => 'View salaries',
                    'salary.update'  => 'Edit salaries',
                    'salary.approve' => 'Approve payroll',
                ],
                'Reports' => [
                    'reports.view'   => 'View HR reports',
                    'reports.export' => 'Export HR reports',
                ],
            ],
            'accounting' => [
                'Invoices' => [
                    'invoices.view'    => 'View invoices',
                    'invoices.create'  => 'Create invoices',
                    'invoices.approve' => 'Approve invoices',
                    'invoices.delete'  => 'Delete invoices',
                ],
                'Payments' => [
                    'payments.view'   => 'View payments',
                    'payments.create' => 'Register payments',
                    'payments.export' => 'Export payments',
                ],
                'Reports' => [
                    'reports.view'   => 'View financial reports',
                    'reports.export' => 'Export financial reports',
                ],
            ],
            'salon' => [
                'Appointments' => [
                    'appointments.view'   => 'View appointments',
                    'appointments.create' => 'Create appointments',
                    'appointments.update' => 'Edit appointments',
                    'appointments.delete' => 'Cancel appointments',
                ],
                'Clients' => [
                    'clients.view'   => 'View clients',
                    'clients.create' => 'Add clients',
                    'clients.update' => 'Edit clients',
                ],
                'Finance' => [
                    'finance.view'    => 'View financial data',
                    'finance.reports' => 'View revenue reports',
                ],
            ],
        ];

        return $permissions[$appSlug] ?? [
            'General' => [
                'view'   => 'View',
                'create' => 'Create',
                'update' => 'Edit',
                'delete' => 'Delete',
                'export' => 'Export',
            ],
        ];
    }
}
