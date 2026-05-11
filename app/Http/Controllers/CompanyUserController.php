<?php

namespace App\Http\Controllers;

use App\Models\Company;

class CompanyUserController extends Controller
{
    public function index(Company $company)
    {
        $users = $company->users()->with('roles')->get();

        return view('companies.users', compact('company', 'users'));
    }
}