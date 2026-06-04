@extends('layouts.app')
@section('title', 'Companies')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Companies</span>
@endsection
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-white">Companies</h1>
        <p class="text-gray-400 text-sm mt-0.5">{{ $companies->count() }} total</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('export.companies') }}"
            class="flex items-center gap-1.5 px-3 py-2 bg-gray-800 hover:bg-gray-700
                border border-gray-700 text-gray-300 text-sm rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </a>
    </div>
</div>

{{-- Add Company --}}
@can('companies.create')
<div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-5">
    <h2 class="text-white font-semibold text-sm mb-3">Add Company</h2>
    <form method="POST" action="{{ route('companies.store') }}"
        class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" required
            placeholder="Company name"
            class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg
                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
        <input type="email" name="email" value="{{ old('email') }}" required
            placeholder="contact@company.com"
            class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg
                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
        <button type="submit"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
            Create
        </button>
    </form>
</div>
@endcan

{{-- Table --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-visible">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-800">
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Company</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">Email</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Users</th>
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
                            <p class="text-gray-600 text-xs">{{ $company->slug }}</p>
                        </div>
                    </div>
                </td>

                {{-- Email --}}
                <td class="px-5 py-3 hidden sm:table-cell text-gray-400 text-sm">
                    {{ $company->email }}
                </td>

                {{-- Users → link to company users list --}}
                <td class="px-5 py-3">
                    <a href="{{ route('companies.users', $company) }}"
                        class="text-blue-400 hover:text-blue-300 text-sm transition">
                        {{ $company->users->count() }} users
                    </a>
                </td>

                {{-- Apps → link to company apps --}}
                <td class="px-5 py-3 hidden md:table-cell">
                    <a href="{{ route('apps.company', $company) }}"
                        class="text-purple-400 hover:text-purple-300 text-sm transition">
                        {{ $company->apps->count() }} apps
                    </a>
                </td>

                {{-- Status --}}
                <td class="px-5 py-3 hidden lg:table-cell">
                    @if($company->is_active)
                        <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                    @else
                        <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                    @endif
                </td>

                {{-- Actions dropdown --}}
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
                            class="absolute top-full right-0 mt-1 w-52 bg-gray-900 border border-gray-700
                                rounded-xl shadow-xl py-1 z-20">

                            {{-- Users --}}
                            <a href="{{ route('companies.users', $company) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                                </svg>
                                View Users
                            </a>

                            {{-- Apps --}}
                            <a href="{{ route('apps.company', $company) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Manage Apps
                            </a>

                            <div class="border-t border-gray-800 my-1"></div>

                            {{-- Edit --}}
                            @can('companies.update')
                            <a href="{{ route('companies.edit', $company) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Company
                            </a>
                            @endcan

                            {{-- Delete --}}
                            @can('companies.delete')
                            <form method="POST" action="{{ route('companies.destroy', $company) }}"
                                onsubmit="return confirm('Delete {{ $company->name }}?')">
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
                            @endcan
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-500 text-sm">No companies yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
