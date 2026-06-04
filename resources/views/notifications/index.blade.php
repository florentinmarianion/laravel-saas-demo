@extends('layouts.app')
@section('title', 'Notifications')
@section('breadcrumbs')
    <span class="text-gray-600">/</span>
    <span class="text-gray-300">Notifications</span>
@endsection
@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-white">Notifications</h1>
    @if(auth()->user()->unreadNotifications->count() > 0)
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf @method('PATCH')
        <button type="submit"
            class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm rounded-lg transition">
            Mark all as read
        </button>
    </form>
    @endif
</div>

<div class="space-y-2">
    @forelse($notifications as $notification)
    @php $isUnread = is_null($notification->read_at); @endphp
    <div class="bg-gray-900 border {{ $isUnread ? 'border-blue-600/30' : 'border-gray-800' }}
        rounded-xl p-4 flex items-start gap-4 transition">
        <div class="w-8 h-8 {{ $isUnread ? 'bg-blue-600/20' : 'bg-gray-800' }}
            rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4 {{ $isUnread ? 'text-blue-400' : 'text-gray-500' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-2.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="{{ $isUnread ? 'text-white' : 'text-gray-400' }} text-sm">
                {{ $notification->data['message'] ?? $notification->type }}
            </p>
            <p class="text-gray-600 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            @if($isUnread)
            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                @csrf @method('PATCH')
                <button type="submit" class="text-blue-400 hover:text-blue-300 text-xs transition">
                    Mark read
                </button>
            </form>
            @else
            <span class="w-2 h-2 rounded-full bg-gray-700"></span>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-10 text-center">
        <svg class="w-10 h-10 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-2.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <p class="text-gray-500 text-sm">No notifications yet.</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-4">{{ $notifications->links() }}</div>
@endif
@endsection
