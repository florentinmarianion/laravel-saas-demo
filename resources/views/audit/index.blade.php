@extends('layouts.app')
@section('title', 'Audit Log')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Audit Log</span>
@endsection
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-white">Audit Log</h1>
    <span class="text-gray-500 text-sm">Last {{ $logs->total() }} actions</span>
</div>

<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-visible">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-800">
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Action</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">User</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden md:table-cell">Details</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden lg:table-cell">IP</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">When</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            @php
                $actionColors = [
                    'created' => 'bg-green-500/10 text-green-400',
                    'updated' => 'bg-blue-500/10 text-blue-400',
                    'deleted' => 'bg-red-500/10 text-red-400',
                    'sent'    => 'bg-purple-500/10 text-purple-400',
                    'toggled' => 'bg-yellow-500/10 text-yellow-400',
                ];
                $actionPart = explode('.', $log->action)[1] ?? $log->action;
                $colorClass = $actionColors[$actionPart] ?? 'bg-gray-500/10 text-gray-400';
            @endphp
            <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                <td class="px-5 py-3">
                    <span class="text-xs px-2 py-1 rounded-full {{ $colorClass }} font-mono">
                        {{ $log->action }}
                    </span>
                </td>
                <td class="px-5 py-3 hidden sm:table-cell">
                    @if($log->user)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-blue-600/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-400 text-xs font-bold">
                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                </span>
                            </div>
                            <a href="{{ route('users.index') }}?search={{ $log->user->email }}"
                                class="text-gray-300 hover:text-white text-sm transition">
                                {{ $log->user->name }}
                            </a>
                        </div>
                    @else
                        <span class="text-gray-600 text-sm">System</span>
                    @endif
                </td>
                <td class="px-5 py-3 hidden md:table-cell">
                    @if($log->data)
                        <div class="flex flex-wrap gap-1">
                            @foreach((array)$log->data as $key => $value)
                                <span class="bg-gray-800 text-gray-400 text-xs px-2 py-0.5 rounded font-mono">
                                    {{ $key }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-gray-600 text-xs">—</span>
                    @endif
                </td>
                <td class="px-5 py-3 hidden lg:table-cell text-gray-500 text-xs font-mono">
                    {{ $log->ip_address ?? '—' }}
                </td>
                <td class="px-5 py-3 text-gray-400 text-sm whitespace-nowrap">
                    {{ $log->created_at->diffForHumans() }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-8 text-center text-gray-500 text-sm">No audit logs yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($logs->hasPages())
    <div class="px-5 py-3 border-t border-gray-800">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
