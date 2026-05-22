<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permissions for {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-white font-semibold">SaaS Platform</span>
                <span class="text-gray-600 text-sm">/ Users</span>
                <span class="text-gray-600 text-sm">/</span>
                <span class="text-gray-400 text-sm">{{ $user->name }}</span>
                <span class="text-gray-600 text-sm">/</span>
                <span class="text-gray-400 text-sm">Permissions</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}" class="text-gray-500 hover:text-white text-sm transition">← Users</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-gray-500 hover:text-white text-sm transition">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-6 py-8">

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3 mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">

            <!-- User Info -->
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-800">
                <div class="w-12 h-12 bg-blue-600/20 rounded-full flex items-center justify-center">
                    <span class="text-blue-400 text-xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg">{{ $user->name }}</h1>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-0.5 rounded-full">
                            {{ $user->roles->first()?->name ?? 'no role' }}
                        </span>
                        @if($user->company)
                            <span class="bg-gray-800 text-gray-400 text-xs px-2 py-0.5 rounded-full">
                                {{ $user->company->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permissions Form -->
            <form method="POST" action="{{ route('users.permissions.update', $user) }}">
                @csrf
                @method('PUT')

                <h2 class="text-white font-semibold mb-4">Assign Permissions</h2>

                @if($allPermissions->isEmpty())
                    <p class="text-gray-500 text-sm">No permissions created yet. <a href="{{ route('permissions.index') }}" class="text-blue-400 hover:text-blue-300">Create some first.</a></p>
                @else
                    @php
                        $grouped = $allPermissions->groupBy(function($p) {
                            return explode('.', $p->name)[0];
                        });
                    @endphp

                    <div class="space-y-6">
                        @foreach($grouped as $module => $permissions)
                        <div class="bg-gray-800/50 rounded-xl p-5">
                            <h3 class="text-gray-300 font-medium text-sm uppercase tracking-wider mb-3">{{ $module }}</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($permissions as $permission)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600 focus:ring-blue-500">
                                    <span class="text-gray-300 text-sm group-hover:text-white transition font-mono">
                                        {{ $permission->name }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                            Save Permissions
                        </button>
                        <a href="{{ route('users.index') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium px-6 py-2.5 rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</body>
</html>