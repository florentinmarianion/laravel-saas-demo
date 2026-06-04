<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Admins skip company context — they see everything
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // If no active company in session, redirect to selection page
        if (! session('active_company_id')) {
            // If user belongs to exactly one company, auto-select it
            $companies = $user->companies()->where('companies.is_active', true)->get();

            if ($companies->count() === 1) {
                session([
                    'active_company_id'   => $companies->first()->id,
                    'active_company_name' => $companies->first()->name,
                ]);
                return $next($request);
            }

            // Multiple companies — let user choose
            if ($companies->count() > 1) {
                return redirect()->route('company.select');
            }

            // No companies at all — access denied
            abort(403, 'You are not assigned to any active company.');
        }

        // Verify the stored company still belongs to the user
        $valid = $user->companies()
            ->where('companies.id', session('active_company_id'))
            ->where('companies.is_active', true)
            ->exists();

        if (! $valid) {
            session()->forget(['active_company_id', 'active_company_name']);
            return redirect()->route('company.select')
                ->withErrors(['company' => 'Your active company is no longer available.']);
        }

        return $next($request);
    }
}
