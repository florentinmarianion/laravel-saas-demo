<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanySwitchController extends Controller
{
    /**
     * Show company selection page.
     * Shown when a user belongs to multiple companies and none is active.
     */
    public function select()
    {
        $user = Auth::user();

        // Admins don't need company context
        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard');
        }

        $companies = $user->companies()
            ->where('is_active', true)
            ->withPivot('role', 'is_active')
            ->get();

        if ($companies->isEmpty()) {
            abort(403, 'You are not assigned to any active company.');
        }

        // Auto-select if only one
        if ($companies->count() === 1) {
            return $this->setCompany($companies->first()->id);
        }

        return view('company.select', compact('companies'));
    }

    /**
     * Switch active company — called from navbar dropdown.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $user = Auth::user();
        $companyId = (int) $request->company_id;

        // Verify user actually belongs to this company
        $company = $user->companies()
            ->where('companies.id', $companyId)
            ->where('is_active', true)
            ->first();

        if (! $company) {
            return back()->withErrors(['company' => 'Invalid company selection.']);
        }

        return $this->setCompany($companyId, $company->name);
    }

    /**
     * Store active company in session and redirect.
     */
    private function setCompany(int $companyId, ?string $companyName = null): \Illuminate\Http\RedirectResponse
    {
        if (! $companyName) {
            $companyName = \App\Models\Company::find($companyId)?->name;
        }

        session([
            'active_company_id'   => $companyId,
            'active_company_name' => $companyName,
        ]);

        return redirect()->route('dashboard')
            ->with('success', "Switched to {$companyName}.");
    }
}
