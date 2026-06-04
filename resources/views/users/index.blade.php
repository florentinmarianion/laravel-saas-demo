@extends('layouts.app')

@section('title', 'Users')

@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Users</span>
@endsection

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-white">Users</h1>
        <p class="text-gray-400 text-sm mt-0.5">{{ $users->total() }} total users</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('export.users') }}"
            class="flex items-center gap-1.5 px-3 py-2 bg-gray-800 hover:bg-gray-700
                border border-gray-700 text-gray-300 text-sm rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </a>
        <a href="{{ route('dashboard') }}#invite"
            class="flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-500
                text-white text-sm rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Invite User
        </a>
    </div>
</div>

<form method="GET" action="{{ route('users.index') }}"
    class="flex flex-col sm:flex-row gap-2 mb-5">
    <input type="text" name="search" value="{{ request('search') }}"
        placeholder="Search name or email..."
        class="flex-1 bg-gray-900 border border-gray-800 text-white rounded-lg
            px-4 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
    <select name="company_id"
        class="bg-gray-900 border border-gray-800 text-white rounded-lg
            px-4 py-2 text-sm focus:outline-none focus:border-blue-500 transition">
        <option value="">All Companies</option>
        @foreach($companies as $company)
            <option value="{{ $company->id }}"
                {{ request('company_id') == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select>
    <button type="submit"
        class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
        Filter
    </button>
    @if(request('search') || request('company_id'))
        <a href="{{ route('users.index') }}"
            class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition text-center">
            Clear
        </a>
    @endif
</form>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-visible">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-800">
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">User</th>
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">Role</th>
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden md:table-cell">Companies</th>
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden lg:table-cell">Status</th>
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden lg:table-cell">Joined</th>
                    <th class="text-right text-gray-400 text-xs font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">

                    {{-- User --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-400 text-xs font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium">{{ $user->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Role --}}
                    <td class="px-5 py-3 hidden sm:table-cell">
                        <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                            {{ $user->roles->first()?->name ?? 'no role' }}
                        </span>
                    </td>

                    {{-- Companies → link spre lista companiilor acestui user --}}
                    <td class="px-5 py-3 hidden md:table-cell">
                        @if($user->companies->count() > 0)
                            <a href="{{ route('user.companies', $user) }}"
                                class="text-blue-400 hover:text-blue-300 text-xs transition">
                                {{ $user->companies->count() }}
                                {{ Str::plural('company', $user->companies->count()) }}
                            </a>
                        @else
                            <span class="text-gray-600 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-3 hidden lg:table-cell">
                        @if($user->is_active)
                            <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                        @else
                            <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                        @endif
                    </td>

                    {{-- Joined --}}
                    <td class="px-5 py-3 hidden lg:table-cell text-gray-400 text-sm">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-3">
                        <div x-data="{ open: false }" class="relative flex justify-end">
                            <button @click="open = !open" @keydown.escape="open = false"
                                class="p-1.5 text-gray-500 hover:text-white hover:bg-gray-700 rounded-md transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-transition
                                class="absolute top-full right-0 mt-1 w-56 bg-gray-900 border border-gray-700
                                    rounded-xl shadow-xl py-1 z-20">

                                {{-- Companies --}}
                                <a href="{{ route('user.companies', $user) }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                    </svg>
                                    Companies
                                </a>

                                {{-- Global Permissions --}}
                                <a href="{{ route('users.permissions', $user) }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                    Global Permissions
                                </a>

                                {{-- Toggle --}}
                                <form method="POST" action="{{ route('users.toggle', $user) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-yellow-400 hover:text-yellow-300 hover:bg-gray-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                    onsubmit="return confirm('Delete {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-gray-800 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-gray-500 text-sm">
                        No users found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    @if($users->hasPages())
    <div class="px-5 py-3 border-t border-gray-800">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
