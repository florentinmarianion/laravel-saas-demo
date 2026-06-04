@extends('layouts.app')
@section('title', 'Apps — ' . $company->name)
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <a href="{{ route('companies.users', $company) }}" class="hover:text-white transition">{{ $company->name }}</a>
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Apps</span>
@endsection
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-white">{{ $company->name }} — Apps</h1>
        <p class="text-gray-400 text-sm mt-0.5">Select which apps this company can access</p>
    </div>
    <a href="{{ route('companies.users', $company) }}"
        class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
        ← Users
    </a>
</div>
<div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
    <form method="POST" action="{{ route('apps.company.sync', $company) }}">
        @csrf @method('PUT')
        @if($allApps->isEmpty())
            <p class="text-gray-500 text-sm">No apps available.
                <a href="{{ route('apps.index') }}" class="text-blue-400">Create some first.</a>
            </p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
            @foreach($allApps as $app)
            @php
                $colors = ['blue'=>'border-blue-600/30 bg-blue-600/10','green'=>'border-green-600/30 bg-green-600/10','yellow'=>'border-yellow-600/30 bg-yellow-600/10','purple'=>'border-purple-600/30 bg-purple-600/10','pink'=>'border-pink-600/30 bg-pink-600/10','red'=>'border-red-600/30 bg-red-600/10','orange'=>'border-orange-600/30 bg-orange-600/10'];
                $c = $colors[$app->color] ?? $colors['blue'];
                $checked = in_array($app->id, $companyAppIds);
            @endphp
            <label class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition
                {{ $checked ? $c : 'border-gray-700 bg-gray-800/30 hover:border-gray-600' }}">
                <input type="checkbox" name="app_ids[]" value="{{ $app->id }}"
                    {{ $checked ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600">
                <div class="flex-1">
                    <p class="text-white text-sm font-medium">{{ $app->name }}</p>
                    <p class="text-gray-400 text-xs">{{ $app->description }}</p>
                </div>
                @if($checked)
                <a href="{{ route('apps.user', [auth()->user(), $company]) }}"
                    class="text-purple-400 hover:text-purple-300 text-xs transition whitespace-nowrap"
                    onclick="event.preventDefault(); window.location='{{ route('apps.user', [auth()->user(), $company]) }}'">
                    User Access
                </a>
                @endif
            </label>
            @endforeach
        </div>
        <div class="flex gap-3">
            <button type="submit"
                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
                Save Apps
            </button>
            <a href="{{ route('companies.users', $company) }}"
                class="px-6 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
                Cancel
            </a>
        </div>
        @endif
    </form>
</div>
@endsection
