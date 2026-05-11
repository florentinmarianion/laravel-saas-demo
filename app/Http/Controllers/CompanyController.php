<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
        ]);

        Company::create([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'slug'  => Str::slug($validated['name']),
        ]);

        return redirect()->route('dashboard')->with('success', 'Company created successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('dashboard')->with('success', 'Company deleted successfully.');
    }
}