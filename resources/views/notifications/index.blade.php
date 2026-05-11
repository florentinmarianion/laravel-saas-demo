<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-white font-semibold">SaaS Platform</span>
                <span class="text-gray-600 text-sm">/ Notifications</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-white text-sm transition">← Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-gray-500 hover:text-white text-sm transition">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-6 py-8">

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-white font-bold text-lg">Notifications</h1>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-gray-400 hover:text-white text-sm transition">
                    Mark all as read
                </button>
            </form>
        </div>

        <div class="space-y-3">
            @forelse($notifications as $notification)
            <div class="bg-gray-900 border {{ is_null($notification->read_at) ? 'border-blue-500/30' : 'border-gray-800' }} rounded-xl p-5 flex items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full mt-1.5 {{ is_null($notification->read_at) ? 'bg-blue-500' : 'bg-gray-700' }} flex-shrink-0"></div>
                    <div>
                        <p class="text-white text-sm">{{ $notification->data['message'] }}</p>
                        <p class="text-gray-500 text-xs mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        @if(isset($notification->data['action_url']))
                            <a href="{{ $notification->data['action_url'] }}"
                                class="text-blue-400 hover:text-blue-300 text-xs mt-2 inline-block transition">
                                {{ $notification->data['action_label'] ?? 'View' }} →
                            </a>
                        @endif
                    </div>
                </div>
                @if(is_null($notification->read_at))
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-gray-500 hover:text-white text-xs transition flex-shrink-0">
                        Mark read
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
                <p class="text-gray-500 text-sm">No notifications yet.</p>
            </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</body>
</html>