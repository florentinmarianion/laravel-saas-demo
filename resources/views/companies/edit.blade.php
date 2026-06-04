@extends('layouts.app')
@section('title', 'Edit — ' . $company->name)
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <a href="{{ route('companies.index') }}" class="hover:text-white transition">Companies</a>
    <span class="text-gray-600">/</span>
    <a href="{{ route('companies.users', $company) }}" class="hover:text-white transition">{{ $company->name }}</a>
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Edit</span>
@endsection
@section('content')

<div class="max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-white">Edit Company</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('companies.users', $company) }}"
                class="px-3 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 text-sm rounded-lg transition">
                Users
            </a>
            <a href="{{ route('apps.company', $company) }}"
                class="px-3 py-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-400 text-sm rounded-lg transition">
                Apps
            </a>
            <a href="{{ route('companies.index') }}"
                class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
                ← Back
            </a>
        </div>
    </div>

    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
        <form method="POST" action="{{ route('companies.update', $company) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">Company Name</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ $company->is_active ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600">
                    <label for="is_active" class="text-gray-300 text-sm cursor-pointer">
                        Company is active
                    </label>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
                    Save Changes
                </button>
                <a href="{{ route('companies.index') }}"
                    class="px-6 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
