<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function companies()
    {
        $companies = Company::withCount('users')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="companies_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($companies) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'Slug', 'Email', 'Users', 'Active', 'Created At']);

            foreach ($companies as $company) {
                fputcsv($file, [
                    $company->id,
                    $company->name,
                    $company->slug,
                    $company->email,
                    $company->users_count,
                    $company->is_active ? 'Yes' : 'No',
                    $company->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function users()
    {
        $users = User::with(['companies', 'roles'])->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'Email', 'Company', 'Role', 'Active', 'Joined At']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->companies->pluck('name')->join(', ') ?: '—',
                    $user->roles->first()?->name ?? 'no role',
                    $user->is_active ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
