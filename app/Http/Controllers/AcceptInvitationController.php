<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\InvitationAcceptedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AcceptInvitationController extends Controller
{
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return view('invitation.expired');
        }

        return view('invitation.accept', compact('invitation'));
    }

    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('login')->withErrors(['invitation' => 'Invitation is expired or already accepted.']);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verifică dacă userul există deja
        $user = User::where('email', $invitation->email)->first();

        if (! $user) {
            $user = User::create([
                'name'       => $validated['name'],
                'email'      => $invitation->email,
                'password'   => Hash::make($validated['password']),
                'company_id' => $invitation->company_id,
                'is_active'  => true,
            ]);

            $user->assignRole($invitation->role);

            if (!empty($invitation->permissions)) {
                $user->givePermissionTo($invitation->permissions);
            }
        }

        // Adaugă în company_user dacă nu există deja
        $exists = DB::table('company_user')
            ->where('user_id', $user->id)
            ->where('company_id', $invitation->company_id)
            ->exists();

        if (! $exists) {
            DB::table('company_user')->insert([
                'user_id'    => $user->id,
                'company_id' => $invitation->company_id,
                'role'       => $invitation->role,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign permissions from invitation if any were selected
        if (!empty($invitation->permissions)) {
            $user->givePermissionTo($invitation->permissions);
        }

        $invitation->load('company');
        $invitation->update(['accepted_at' => now()]);

        // Notify all admins
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new InvitationAcceptedNotification($invitation));
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome! Your account has been created.');
    }
}
