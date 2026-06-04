@extends('layouts.app')
@section('title', $user->name . ' — Apps @ ' . $company->name)
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <a href="{{ route('users.index') }}" class="hover:text-white transition">Users</a>
    <span class="text-gray-600">/</span>
    <a href="{{ route('user.companies', $user) }}" class="hover:text-white transition">{{ $user->name }}</a>
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">{{ $company->name }} — Apps</span>
@endsection
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 bg-blue-600/20 rounded-full flex items-center justify-center">
                <span class="text-blue-400 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <h1 class="text-xl font-bold text-white">{{ $user->name }}</h1>
            <span class="text-gray-500 text-sm">@</span>
            <span class="text-white text-sm font-medium">{{ $company->name }}</span>
        </div>
        <p class="text-gray-400 text-sm">Select which apps this user can access in this company</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('users.permissions', $user) }}"
            class="px-3 py-2 bg-yellow-600/20 hover:bg-yellow-600/30 text-yellow-400 text-sm rounded-lg transition">
            Global Permissions
        </a>
        <a href="{{ route('user.companies', $user) }}"
            class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
            ← Companies
        </a>
    </div>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
    <form method="POST" action="{{ route('apps.user.sync', [$user, $company]) }}">
        @csrf @method('PUT')
        @if($companyApps->isEmpty())
            <p class="text-gray-500 text-sm">
                No apps enabled for {{ $company->name }}.
                <a href="{{ route('apps.company', $company) }}" class="text-blue-400 hover:text-blue-300">
                    Enable apps for this company first.
                </a>
            </p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
            @foreach($companyApps as $app)
            @php
                $colors = ['blue'=>'border-blue-600/30 bg-blue-600/10 text-blue-400','green'=>'border-green-600/30 bg-green-600/10 text-green-400','yellow'=>'border-yellow-600/30 bg-yellow-600/10 text-yellow-400','purple'=>'border-purple-600/30 bg-purple-600/10 text-purple-400','pink'=>'border-pink-600/30 bg-pink-600/10 text-pink-400','red'=>'border-red-600/30 bg-red-600/10 text-red-400','orange'=>'border-orange-600/30 bg-orange-600/10 text-orange-400'];
                $c = $colors[$app->color] ?? $colors['blue'];
                $checked = in_array($app->id, $userAppIds);
            @endphp
            <div class="rounded-xl border {{ $checked ? $c : 'border-gray-700 bg-gray-800/30' }} transition">
                <label class="flex items-center gap-4 p-4 cursor-pointer">
                    <input type="checkbox" name="app_ids[]" value="{{ $app->id }}"
                        {{ $checked ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600">
                    <div class="flex-1">
                        <p class="text-white text-sm font-medium">{{ $app->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $app->description }}</p>
                    </div>
                </label>
                @if($checked)
                <div class="px-4 pb-3 flex justify-end">
                    <a href="{{ route('app.permissions.show', [$user, $company, $app]) }}"
                        class="text-xs text-purple-400 hover:text-purple-300 transition">
                        Manage Permissions →
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
                Save Apps
            </button>
            <a href="{{ route('user.companies', $user) }}"
                class="px-6 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
                Cancel
            </a>
        </div>
        @endif
    </form>
</div>
@endsection
