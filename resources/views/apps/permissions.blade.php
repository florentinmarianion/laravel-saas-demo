<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Permissions — {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen">

    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-white font-semibold">SaaS Platform</span>
                <span class="text-gray-600">/</span>
                <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-white transition">Users</a>
                <span class="text-gray-600">/</span>
                <span class="text-gray-400">{{ $user->name }}</span>
                <span class="text-gray-600">/</span>
                <span class="text-gray-400">{{ $company->name }}</span>
                <span class="text-gray-600">/</span>
                <span class="text-gray-300">{{ $app->name }} Permissions</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('apps.user', [$user, $company]) }}" class="text-gray-500 hover:text-white text-sm transition">
                    ← Back to Apps
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-gray-500 hover:text-white text-sm transition">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-6 py-8">

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3 mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header: User + Company + App --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
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
                            <span class="text-gray-600 text-xs">at</span>
                            <span class="bg-gray-800 text-gray-300 text-xs px-2 py-0.5 rounded-full">
                                {{ $company->name }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    @php
                        $colors = [
                            'blue'   => 'bg-blue-600/20 text-blue-400 border-blue-600/30',
                            'green'  => 'bg-green-600/20 text-green-400 border-green-600/30',
                            'yellow' => 'bg-yellow-600/20 text-yellow-400 border-yellow-600/30',
                            'purple' => 'bg-purple-600/20 text-purple-400 border-purple-600/30',
                            'pink'   => 'bg-pink-600/20 text-pink-400 border-pink-600/30',
                        ];
                        $colorClass = $colors[$app->color] ?? $colors['blue'];
                    @endphp
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border {{ $colorClass }}">
                        <span class="font-semibold">{{ $app->name }}</span>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">{{ $app->description }}</p>
                </div>
            </div>
        </div>

        {{-- Permissions Form --}}
        <form method="POST" action="{{ route('app.permissions.update', [$user, $company, $app]) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                @forelse($availablePermissions as $group => $permissions)
                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                    <div class="px-6 py-3 border-b border-gray-800 flex items-center justify-between">
                        <h2 class="text-white font-semibold text-sm uppercase tracking-wider">{{ $group }}</h2>
                        <button type="button"
                            onclick="toggleGroup('group-{{ Str::slug($group) }}')"
                            class="text-gray-500 hover:text-white text-xs transition">
                            Toggle all
                        </button>
                    </div>
                    <div id="group-{{ Str::slug($group) }}" class="p-6 grid grid-cols-2 gap-3">
                        @foreach($permissions as $key => $label)
                        @php $checked = in_array($key, $userPermissions); @endphp
                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition
                            {{ $checked ? 'border-blue-600/40 bg-blue-600/10' : 'border-gray-700 bg-gray-800/30 hover:border-gray-600' }}
                            group">
                            <input type="checkbox"
                                name="permissions[]"
                                value="{{ $key }}"
                                {{ $checked ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600
                                       focus:ring-blue-500 focus:ring-offset-gray-900"
                                onchange="this.closest('label').className = this.checked
                                    ? 'flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition border-blue-600/40 bg-blue-600/10 group'
                                    : 'flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition border-gray-700 bg-gray-800/30 hover:border-gray-600 group'">
                            <div>
                                <p class="text-white text-sm font-medium">{{ $label }}</p>
                                <p class="text-gray-500 text-xs font-mono">{{ $key }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
                    <p class="text-gray-500 text-sm">No permissions defined for this app yet.</p>
                </div>
                @endforelse
            </div>

            @if($availablePermissions)
            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Save Permissions
                </button>
                <a href="{{ route('apps.user', [$user, $company]) }}"
                    class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Cancel
                </a>
                <span class="text-gray-500 text-xs ml-2">
                    {{ count($userPermissions) }} permission(s) currently granted
                </span>
            </div>
            @endif
        </form>
    </div>

    <script>
        function toggleGroup(groupId) {
            const group = document.getElementById(groupId);
            const checkboxes = group.querySelectorAll('input[type="checkbox"]');
            const allChecked = [...checkboxes].every(cb => cb.checked);
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
                cb.dispatchEvent(new Event('change'));
            });
        }
    </script>
</body>
</html>
