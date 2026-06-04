@extends('layouts.app')
@section('title', $user->name . ' — Permissions')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <a href="{{ route('users.index') }}" class="hover:text-white transition">Users</a>
    <span class="text-gray-600">/</span>
    <a href="{{ route('user.companies', $user) }}" class="hover:text-white transition">{{ $user->name }}</a>
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Global Permissions</span>
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
        <a href="{{ route('user.companies', $user) }}"
            class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
            ← Companies
        </a>
    </div>
</div>

@if($user->hasRole('admin'))
<div class="bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm rounded-lg p-4 mb-6">
    This user is an <strong>admin</strong> — bypasses all permission checks automatically.
</div>
@endif

<form method="POST" action="{{ route('users.permissions.update', $user) }}">
    @csrf @method('PUT')
    @php $grouped = $allPermissions->groupBy(fn($p) => explode('.', $p->name)[0]); @endphp
    <div class="space-y-4">
        @foreach($grouped as $module => $permissions)
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-800 flex items-center justify-between">
                <h2 class="text-white font-semibold text-sm uppercase tracking-wider">{{ $module }}</h2>
                <button type="button"
                    onclick="toggleGroup('grp-{{ $module }}')"
                    class="text-gray-500 hover:text-white text-xs transition">
                    Toggle all
                </button>
            </div>
            <div id="grp-{{ $module }}" class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($permissions as $permission)
                @php $checked = in_array($permission->name, $userPermissions); @endphp
                <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition
                    {{ $checked ? 'border-blue-600/40 bg-blue-600/10' : 'border-gray-700 bg-gray-800/30 hover:border-gray-600' }}">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                        {{ $checked ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600"
                        onchange="this.closest('label').className = this.checked
                            ? 'flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition border-blue-600/40 bg-blue-600/10'
                            : 'flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition border-gray-700 bg-gray-800/30 hover:border-gray-600'">
                    <span class="text-white text-sm font-mono">{{ $permission->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-5 flex gap-3">
        <button type="submit"
            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
            Save Permissions
        </button>
        <a href="{{ route('user.companies', $user) }}"
            class="px-6 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
            Cancel
        </a>
        <span class="text-gray-500 text-xs self-center">
            {{ count($userPermissions) }} permission(s) granted
        </span>
    </div>
</form>
@endsection
@push('scripts')
<script>
function toggleGroup(id) {
    const boxes = document.getElementById(id).querySelectorAll('input[type="checkbox"]');
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => { b.checked = !allChecked; b.dispatchEvent(new Event('change')); });
}
</script>
@endpush
