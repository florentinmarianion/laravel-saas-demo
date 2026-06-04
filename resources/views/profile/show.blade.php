@extends('layouts.app')
@section('title', 'Profile')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Profile</span>
@endsection
@section('content')

<div class="max-w-2xl mx-auto space-y-5">

    {{-- Profile Info --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-14 h-14 bg-blue-600/20 rounded-full flex items-center justify-center">
                <span class="text-blue-400 text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
            </div>
            <div>
                <h1 class="text-white font-bold text-lg">{{ $user->name }}</h1>
                <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-0.5 rounded-full">
                        {{ $user->roles->first()?->name ?? 'no role' }}
                    </span>
                    @if($user->company)
                    <span class="bg-gray-800 text-gray-300 text-xs px-2 py-0.5 rounded-full">
                        {{ $user->company->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
                    Update Profile
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h2 class="text-white font-semibold mb-5">Change Password</h2>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    @error('current_password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">New Password</label>
                    <input type="password" name="password" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>
            </div>
            <div class="mt-5">
                <button type="submit"
                    class="px-6 py-2.5 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded-lg transition">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Companies --}}
    @if($user->companies->count() > 0)
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <h2 class="text-white font-semibold mb-4">My Companies</h2>
        <div class="space-y-2">
            @foreach($user->companies as $company)
            <div class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center">
                        <span class="text-blue-400 text-xs font-bold">
                            {{ strtoupper(substr($company->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium">{{ $company->name }}</p>
                        <p class="text-gray-500 text-xs">{{ $company->pivot?->role ?? 'member' }}</p>
                    </div>
                </div>
                @if(session('active_company_id') != $company->id)
                <form method="POST" action="{{ route('company.switch') }}">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    <button type="submit"
                        class="text-blue-400 hover:text-blue-300 text-xs transition">
                        Switch
                    </button>
                </form>
                @else
                <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
