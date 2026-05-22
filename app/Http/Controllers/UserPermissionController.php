<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    public function show(User $user)
    {
        $allPermissions  = Permission::orderBy('name')->get();
        
        // Daca e admin, arata toate ca bifate
        if ($user->hasRole('admin')) {
            $userPermissions = $allPermissions->pluck('name')->toArray();
        } else {
            $userPermissions = $user->getDirectPermissions()->pluck('name')->toArray();
        }

        return view('permissions.user', compact('user', 'allPermissions', 'userPermissions'));
    }

    public function update(Request $request, User $user)
    {
        $permissions = $request->input('permissions', []);

        $user->syncPermissions($permissions);

        return redirect()->route('users.permissions', $user)->with('success', 'Permissions updated for ' . $user->name);
    }
}