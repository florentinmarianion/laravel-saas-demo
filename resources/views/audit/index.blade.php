<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log — SaaS Platform</title>
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
                <span class="text-gray-600 text-sm">/ Audit Log</span>
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

    <div class="max-w-6xl mx-auto px-6 py-8">

        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-white font-semibold">Audit Log</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Time</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">User</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Action</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Details</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($log->user)
                                <span class="text-white">{{ $log->user->name }}</span>
                            @else
                                <span class="text-gray-500">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $colors = [
                                    'company.created'      => 'bg-green-500/10 text-green-400',
                                    'company.updated'      => 'bg-blue-500/10 text-blue-400',
                                    'company.deleted'      => 'bg-red-500/10 text-red-400',
                                    'invitation.sent'      => 'bg-yellow-500/10 text-yellow-400',
                                    'invitation.cancelled' => 'bg-orange-500/10 text-orange-400',
                                    'user.toggled'         => 'bg-purple-500/10 text-purple-400',
                                    'user.deleted'         => 'bg-red-500/10 text-red-400',
                                ];
                                $color = $colors[$log->action] ?? 'bg-gray-500/10 text-gray-400';
                            @endphp
                            <span class="text-xs px-2 py-1 rounded-full {{ $color }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">
                            @if($log->data)
                                @foreach($log->data as $key => $value)
                                    <span class="text-gray-500">{{ $key }}:</span>
                                    <span class="text-gray-300">{{ $value }}</span>
                                    @if(!$loop->last) · @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No activity yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</body>
</html>