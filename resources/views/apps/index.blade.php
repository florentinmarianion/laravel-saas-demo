@extends('layouts.app')
@section('title', 'Apps')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Apps</span>
@endsection
@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-xl font-bold text-white">Apps</h1>
</div>
<div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-6">
    <h2 class="text-white font-semibold text-sm mb-4">Create New App</h2>
    <form method="POST" action="{{ route('apps.store') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="App name"
            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
        <input type="text" name="description" value="{{ old('description') }}" placeholder="Short description"
            class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
        <div class="flex gap-2">
            <select name="color" required
                class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                <option value="blue">Blue</option>
                <option value="green">Green</option>
                <option value="yellow">Yellow</option>
                <option value="purple">Purple</option>
                <option value="pink">Pink</option>
                <option value="red">Red</option>
                <option value="orange">Orange</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
                Create
            </button>
        </div>
    </form>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($apps as $app)
    @php
        $colors = ['blue'=>'bg-blue-600/20 text-blue-400 border-blue-600/30','green'=>'bg-green-600/20 text-green-400 border-green-600/30','yellow'=>'bg-yellow-600/20 text-yellow-400 border-yellow-600/30','purple'=>'bg-purple-600/20 text-purple-400 border-purple-600/30','pink'=>'bg-pink-600/20 text-pink-400 border-pink-600/30','red'=>'bg-red-600/20 text-red-400 border-red-600/30','orange'=>'bg-orange-600/20 text-orange-400 border-orange-600/30'];
        $c = $colors[$app->color] ?? $colors['blue'];
    @endphp
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-lg border {{ $c }} flex items-center justify-center text-sm font-bold">
                {{ strtoupper(substr($app->name, 0, 2)) }}
            </div>
            <span class="{{ $app->is_active ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }} text-xs px-2 py-1 rounded-full">
                {{ $app->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <h3 class="text-white font-semibold mb-0.5">{{ $app->name }}</h3>
        <p class="text-gray-500 text-xs mb-3">{{ $app->description }}</p>
        <p class="text-gray-600 text-xs mb-4">
            <a href="{{ route('apps.company', ['company' => $app->companies->first()]) }}" class="hover:text-gray-400 transition">
                {{ $app->companies_count }} {{ Str::plural('company', $app->companies_count) }}
            </a>
        </p>
        <div class="flex items-center gap-3 pt-3 border-t border-gray-800">
            <form method="POST" action="{{ route('apps.toggle', $app) }}">
                @csrf @method('PATCH')
                <button type="submit" class="text-yellow-400 hover:text-yellow-300 text-xs transition">
                    {{ $app->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            <form method="POST" action="{{ route('apps.destroy', $app) }}"
                onsubmit="return confirm('Delete {{ $app->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
        <p class="text-gray-500 text-sm">No apps yet.</p>
    </div>
    @endforelse
</div>
@endsection
