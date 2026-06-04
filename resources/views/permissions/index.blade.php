@extends('layouts.app')
@section('title', 'Permissions')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Permissions</span>
@endsection
@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h1 class="text-xl font-bold text-white">Global Permissions</h1>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-5">
    <h2 class="text-white font-semibold text-sm mb-3">Add Permission</h2>
    <form method="POST" action="{{ route('permissions.store') }}" class="flex gap-3">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" required
            placeholder="e.g. accounting.invoices.approve"
            class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg
                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition font-mono">
        <button type="submit"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
            Add
        </button>
    </form>
    <p class="text-gray-600 text-xs mt-2">Pattern: <span class="font-mono">{module}.{resource}.{action}</span>
        — e.g. <span class="font-mono text-gray-500">currency.export</span>,
        <span class="font-mono text-gray-500">companies.delete</span></p>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-visible">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-800">
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Permission</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">Module</th>
                <th class="text-right text-gray-400 text-xs font-medium px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @php $grouped = $permissions->groupBy(fn($p) => explode('.', $p->name)[0]); @endphp
            @forelse($permissions as $permission)
            <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                <td class="px-5 py-3">
                    <span class="text-white text-sm font-mono">{{ $permission->name }}</span>
                </td>
                <td class="px-5 py-3 hidden sm:table-cell">
                    <span class="bg-gray-800 text-gray-400 text-xs px-2 py-1 rounded-full font-mono">
                        {{ explode('.', $permission->name)[0] }}
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <form method="POST" action="{{ route('permissions.destroy', $permission) }}"
                        onsubmit="return confirm('Delete permission {{ $permission->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-5 py-8 text-center text-gray-500 text-sm">No permissions defined.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($permissions->hasPages())
    <div class="px-5 py-3 border-t border-gray-800">{{ $permissions->links() }}</div>
    @endif
</div>
@endsection
