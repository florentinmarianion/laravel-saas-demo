<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\AuditLog;
class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
        ]);

        $company = Company::create([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'slug'  => Str::slug($validated['name']),
        ]);

        AuditLog::record('company.created', $company, ['name' => $company->name]);
        return redirect()->route('dashboard')->with('success', 'Company created successfully.');
    }

    public function destroy(Company $company)
    {
        AuditLog::record('company.deleted', $company, ['name' => $company->name]);
        $company->delete();

        return redirect()->route('dashboard')->with('success', 'Company deleted successfully.');
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:companies,email,' . $company->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $company->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'slug'      => \Illuminate\Support\Str::slug($validated['name']),
            'is_active' => $request->has('is_active'),
        ]);

        AuditLog::record('company.updated', $company, ['name' => $company->name]);
        return redirect()->route('dashboard')->with('success', 'Company updated successfully.');
    }
}