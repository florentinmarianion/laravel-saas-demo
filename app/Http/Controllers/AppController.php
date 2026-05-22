<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppController extends Controller
{
    // List all apps
    public function index()
    {
        $apps = App::withCount('companies')->orderBy('name')->get();

        return view('apps.index', compact('apps'));
    }

    // Create a new app
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:apps,name',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|in:blue,green,yellow,purple,pink,red,orange',
        ]);

        App::create([
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon'        => Str::slug($validated['name']),
            'color'       => $validated['color'],
            'is_active'   => true,
        ]);

        return redirect()->route('apps.index')->with('success', 'App created successfully.');
    }

    // Toggle app active status
    public function toggle(App $app)
    {
        $app->update(['is_active' => !$app->is_active]);

        return redirect()->back()->with('success', 'App status updated.');
    }

    // Delete app
    public function destroy(App $app)
    {
        $app->delete();

        return redirect()->route('apps.index')->with('success', 'App deleted.');
    }

    // Show company app assignment page
    public function companyApps(Company $company)
    {
        $allApps         = App::where('is_active', true)->orderBy('name')->get();
        $companyAppIds   = $company->apps()->pluck('apps.id')->toArray();

        return view('apps.company', compact('company', 'allApps', 'companyAppIds'));
    }

    // Sync apps for a company
    public function syncCompanyApps(Request $request, Company $company)
    {
        $appIds = $request->input('app_ids', []);
        $company->apps()->sync($appIds);

        return redirect()->route('apps.company', $company)->with('success', 'Apps updated for ' . $company->name);
    }

    // Show user app assignment page per company
    public function userApps(User $user, Company $company)
    {
        $companyApps   = $company->apps()->where('is_active', true)->get();
        $userAppIds    = $user->apps()->wherePivot('company_id', $company->id)->pluck('apps.id')->toArray();

        return view('apps.user', compact('user', 'company', 'companyApps', 'userAppIds'));
    }

    // Sync apps for a user in a company
    public function syncUserApps(Request $request, User $user, Company $company)
    {
        $appIds = $request->input('app_ids', []);

        // Remove existing entries for this user+company
        $user->apps()->wherePivot('company_id', $company->id)->detach();

        // Attach new ones
        foreach ($appIds as $appId) {
            $user->apps()->attach($appId, ['company_id' => $company->id]);
        }

        return redirect()->route('apps.user', [$user, $company])->with('success', 'Apps updated for ' . $user->name);
    }
}