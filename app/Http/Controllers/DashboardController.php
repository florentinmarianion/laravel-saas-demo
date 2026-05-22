<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{

    public function index()
    {
        $stats = [
            'companies'   => Company::count(),
            'users'       => User::count(),
            'invitations' => Invitation::whereNull('accepted_at')->count(),
        ];

        $companies      = Company::with('users')->latest()->get();
        $invitations    = Invitation::with('company')->latest()->get();
        $allPermissions = Permission::orderBy('name')->get();

        // Chart data
        $companiesChart = Company::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $usersChart = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $dates = collect();
        for ($i = 29; $i >= 0; $i--) {
            $dates->push(now()->subDays($i)->format('Y-m-d'));
        }

        $companiesData = $dates->map(fn($date) => $companiesChart[$date] ?? 0);
        $usersData     = $dates->map(fn($date) => $usersChart[$date] ?? 0);
        $dateLabels    = $dates->map(fn($date) => date('M d', strtotime($date)));

        $users = auth()->user()->hasRole('admin') 
        ? collect() 
        : User::with('roles')->where('company_id', auth()->user()->company_id)->get();

        return view('dashboard', compact(
            'stats', 'companies', 'invitations', 'allPermissions', 'users',
            'companiesData', 'usersData', 'dateLabels'
        ));
    }
}