<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;

class CompanyUserController extends Controller
{
    public function index(Company $company)
    {
        $users = $company->users()->with('roles')->get();
        return view('companies.users', compact('company', 'users'));
    }

    public function userCompanies(User $user)
    {
        $companies = $user->companies()->with('apps')->get();
        return view('users.companies', compact('user', 'companies'));
    }
}
