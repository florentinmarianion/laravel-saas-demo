@extends('layouts.app')
@section('title', $company->name . ' — Users')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">{{ $company->name }}</span>
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Users</span>
@endsection
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-white">{{ $company->name }}</h1>
        <p class="text-gray-400 text-sm mt-0.5">{{ $company->email }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('apps.company', $company) }}"
            class="px-3 py-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-400 text-sm rounded-lg transition">
            Manage Apps
        </a>
        <a href="{{ route('companies.edit', $company) }}"
            class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
            Edit Company
        </a>
    </div>
</div>
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-visible">
    <div class="px-5 py-3.5 border-b border-gray-800">
        <h2 class="text-white font-semibold text-sm">Users ({{ $users->count() }})</h2>
    </div>
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-800">
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">User</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">Role</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden md:table-cell">Status</th>
                <th class="text-right text-gray-400 text-xs font-medium px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-600/20 rounded-full flex items-center justify-center">
                            <span class="text-blue-400 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">{{ $user->name }}</p>
                            <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3 hidden sm:table-cell">
                    <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                        {{ $user->roles->first()?->name ?? 'no role' }}
                    </span>
                </td>
                <td class="px-5 py-3 hidden md:table-cell">
                    @if($user->is_active)
                        <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                    @else
                        <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('apps.user', [$user, $company]) }}"
                            class="text-purple-400 hover:text-purple-300 text-xs transition">Apps</a>
                        <a href="{{ route('users.permissions', $user) }}"
                            class="text-yellow-400 hover:text-yellow-300 text-xs transition">Permissions</a>
                        <a href="{{ route('users.index') }}?search={{ $user->email }}"
                            class="text-gray-400 hover:text-white text-xs transition">Profile</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500 text-sm">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
