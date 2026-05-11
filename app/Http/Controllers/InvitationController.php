<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\AuditLog;

class InvitationController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'email'      => 'required|email|unique:users,email',
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|in:admin,member',
        ]);

        $invitation = Invitation::updateOrCreate(
            ['company_id' => $validated['company_id'], 'email' => $validated['email']],
            [
                'role'       => $validated['role'],
                'token'      => Str::random(64),
                'expires_at' => now()->addDays(7),
                'accepted_at' => null,
            ]
        );

        $invitation->load('company');
        AuditLog::record('invitation.sent', $invitation, [
            'email' => $invitation->email,
            'role'  => $invitation->role,
            'company' => $invitation->company->name,
        ]);
        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return redirect()->route('dashboard')->with('success', 'Invitation sent to ' . $validated['email']);
    }

    public function destroy(Invitation $invitation)
    {
        AuditLog::record('invitation.cancelled', $invitation, [
            'email' => $invitation->email,
        ]);
        $invitation->delete();

        return redirect()->route('dashboard')->with('success', 'Invitation cancelled.');
    }
}