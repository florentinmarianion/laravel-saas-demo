<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'      => 'required|email|unique:users,email',
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|in:admin,member',
        ]);

        $invitation = Invitation::create([
            'company_id' => $validated['company_id'],
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'token'      => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        // Aici vei trimite email-ul cu link-ul de invitatie
        // Mail::to($invitation->email)->send(new InvitationMail($invitation));

        return response()->json([
            'message' => 'Invitation sent successfully.',
            'token'   => $invitation->token,
        ], 201);
    }

    public function accept(Request $request, string $token): JsonResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return response()->json([
                'message' => 'Invitation is expired or already accepted.',
            ], 422);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
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

        return response()->json([
            'message' => 'Account created successfully.',
            'user'    => $user,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $invitations = Invitation::where('company_id', $request->user()->company_id)
            ->latest()
            ->get();

        return response()->json($invitations);
    }
}