<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['company', 'roles'])->latest();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users     = $query->paginate(20);
        $companies = Company::orderBy('name')->get();

        return view('users.index', compact('users', 'companies'));
    }

    public function toggle(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('users.index')->with('success', 'User status updated.');
        //return redirect()->back()->with('success', 'User status updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
        //return redirect()->back()->with('success', 'User deleted.');
    }
}