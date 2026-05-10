<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'companies'   => Company::count(),
            'users'       => User::count(),
            'invitations' => Invitation::whereNull('accepted_at')->count(),
        ];

        $companies = Company::with('users')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'companies'));
    }
}