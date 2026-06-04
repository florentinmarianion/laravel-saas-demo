@extends('layouts.app')
@section('title', $user->name . ' — Companies')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <a href="{{ route('users.index') }}" class="hover:text-white transition">Users</a>
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">{{ $user->name }}</span>
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Companies</span>
@endsection
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 bg-blue-600/20 rounded-full flex items-center justify-center">
                <span class="text-blue-400 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <h1 class="text-xl font-bold text-white">{{ $user->name }}</h1>
            <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                {{ $user->roles->first()?->name ?? 'no role' }}
            </span>
        </div>
        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('users.permissions', $user) }}"
            class="px-3 py-2 bg-yellow-600/20 hover:bg-yellow-600/30 text-yellow-400 text-sm rounded-lg transition">
            Global Permissions
        </a>
        <a href="{{ route('users.index') }}"
            class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
            ← All Users
        </a>
    </div>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl">
    <div class="px-5 py-3.5 border-b border-gray-800">
        <h2 class="text-white font-semibold text-sm">
            Companies ({{ $companies->count() }})
        </h2>
    </div>
    <div class="overflow-visible">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-800">
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Company</th>
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">Role</th>
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden md:table-cell">Apps</th>
                    <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden lg:table-cell">Status</th>
                    <th class="text-right text-gray-400 text-xs font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">

                    {{-- Company --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 bg-blue-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-400 text-xs font-bold">
                                    {{ strtoupper(substr($company->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium">{{ $company->name }}</p>
                                <p class="text-gray-600 text-xs">{{ $company->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Role in this company --}}
                    <td class="px-5 py-3 hidden sm:table-cell">
                        <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                            {{ $company->pivot?->role ?? '—' }}
                        </span>
                    </td>

                    {{-- Apps --}}
                    <td class="px-5 py-3 hidden md:table-cell">
                        <div class="flex flex-wrap gap-1">
                            @forelse($company->apps as $app)
                                <a href="{{ route('app.permissions.show', [$user, $company, $app]) }}"
                                    class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs px-2 py-0.5 rounded-full transition">
                                    {{ $app->name }}
                                </a>
                            @empty
                                <span class="text-gray-600 text-xs">No apps</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-5 py-3 hidden lg:table-cell">
                        @if($company->is_active)
                            <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                        @else
                            <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                        @endif
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

                                {{-- View all users in this company --}}
                                <a href="{{ route('companies.users', $company) }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                                    </svg>
                                    All Users in Company
                                </a>

                                {{-- Apps assigned to user in this company --}}
                                <a href="{{ route('apps.user', [$user, $company]) }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                                    </svg>
                                    User's Apps here
                                </a>

                                {{-- Manage company apps --}}
                                <a href="{{ route('apps.company', $company) }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    </svg>
                                    Manage Company Apps
                                </a>

                                <div class="border-t border-gray-800 my-1"></div>

                                {{-- Edit company --}}
                                <a href="{{ route('companies.edit', $company) }}"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit Company
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-500 text-sm">
                        This user is not assigned to any company.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
