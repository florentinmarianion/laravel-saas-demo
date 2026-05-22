<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permissions — SaaS Platform</title>
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
                <span class="text-gray-600 text-sm">/ Permissions</span>
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

    <div class="max-w-4xl mx-auto px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Create Permission -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-white font-semibold mb-4">Create Permission</h2>
            <form method="POST" action="{{ route('permissions.store') }}" class="flex gap-3">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="e.g. currency.view, hr.view, accounting.view"
                    class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Create
                </button>
            </form>
            <p class="text-gray-500 text-xs mt-2">Use dot notation for clarity: <span class="text-gray-400">module.action</span> (e.g. currency.view, employees.edit)</p>
        </div>

        <!-- Permissions List -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <h2 class="text-white font-semibold">All Permissions ({{ $permissions->total() }})</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Permission Name</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Assigned to Users</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Assigned to Roles</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Created</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4">
                            <span class="bg-purple-600/20 text-purple-400 text-xs px-2 py-1 rounded-full font-mono">
                                {{ $permission->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            {{ $permission->users->count() }} users
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            {{ $permission->roles->count() }} roles
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            {{ $permission->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('permissions.destroy', $permission) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Delete permission {{ $permission->name }}?')"
                                    class="text-red-400 hover:text-red-300 text-xs transition">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No permissions yet. Create your first one above.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            @if($permissions->hasPages())
            <div class="px-6 py-4 border-t border-gray-800">
                {{ $permissions->links() }}
            </div>
            @endif
        </div>
    </div>
</body>
</html>