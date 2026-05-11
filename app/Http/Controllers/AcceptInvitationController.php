<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
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
            'name'                  => 'required|string|max:255',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $invitation->email,
            'password'   => Hash::make($validated['password']),
            'company_id' => $invitation->company_id,
            'is_active'  => true,
        ]);

        $user->assignRole($invitation->role);

        $invitation->update(['accepted_at' => now()]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome! Your account has been created.');
    }
}