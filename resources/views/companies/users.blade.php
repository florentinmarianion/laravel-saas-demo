<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->name }} — Users</title>
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
                <span class="text-gray-600 text-sm">/ Dashboard</span>
                <span class="text-gray-600 text-sm">/</span>
                <span class="text-gray-400 text-sm">{{ $company->name }}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-white text-sm transition">
                    ← Back to Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-gray-500 hover:text-white text-sm transition">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-8">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-white text-xl font-bold">{{ $company->name }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ $company->email }}</p>
            </div>
            <span class="{{ $company->is_active ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }} text-xs px-3 py-1 rounded-full">
                {{ $company->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <!-- Users Table -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-white font-semibold">Users ({{ $users->count() }})</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Name</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Email</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Role</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Status</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-full flex items-center justify-center">
                                    <span class="text-blue-400 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="text-white text-sm">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                                {{ $user->roles->first()->name ?? 'no role' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                            @else
                                <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>