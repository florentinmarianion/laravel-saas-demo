<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        $companies = Company::with('users')->get();

        return response()->json($companies);
    }

    public function store(Request $request): JsonResponse
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

        return response()->json($company, 201);
    }

    public function show(Company $company): JsonResponse
    {
        return response()->json($company->load('users'));
    }

    public function update(Request $request, Company $company): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:companies,email,' . $company->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $company->update($validated);

        return response()->json($company);
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return response()->json(['message' => 'Company deleted successfully.']);
    }
}