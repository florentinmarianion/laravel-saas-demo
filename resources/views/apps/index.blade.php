<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apps — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen">

    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-white font-semibold">SaaS Platform</span>
                <span class="text-gray-600 text-sm">/ Apps</span>
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

    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

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

        <!-- Create App Form -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <h2 class="text-white font-semibold mb-4">Create New App</h2>
            <form method="POST" action="{{ route('apps.store') }}">
                @csrf
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">App Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition"
                            placeholder="e.g. Payroll Manager">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <input type="text" name="description" value="{{ old('description') }}"
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition"
                            placeholder="Short description">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Color</label>
                        <select name="color" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                            <option value="blue">Blue</option>
                            <option value="green">Green</option>
                            <option value="yellow">Yellow</option>
                            <option value="purple">Purple</option>
                            <option value="pink">Pink</option>
                            <option value="red">Red</option>
                            <option value="orange">Orange</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        Create App
                    </button>
                </div>
            </form>
        </div>

        <!-- Apps Grid -->
        <div class="grid grid-cols-3 gap-4">
            @forelse($apps as $app)
            @php
                $colors = [
                    'blue'   => 'bg-blue-600/20 text-blue-400 border-blue-600/30',
                    'green'  => 'bg-green-600/20 text-green-400 border-green-600/30',
                    'yellow' => 'bg-yellow-600/20 text-yellow-400 border-yellow-600/30',
                    'purple' => 'bg-purple-600/20 text-purple-400 border-purple-600/30',
                    'pink'   => 'bg-pink-600/20 text-pink-400 border-pink-600/30',
                    'red'    => 'bg-red-600/20 text-red-400 border-red-600/30',
                    'orange' => 'bg-orange-600/20 text-orange-400 border-orange-600/30',
                ];
                $colorClass = $colors[$app->color] ?? $colors['blue'];
            @endphp
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg border {{ $colorClass }} flex items-center justify-center text-lg font-bold">
                        {{ strtoupper(substr($app->name, 0, 2)) }}
                    </div>
                    <span class="{{ $app->is_active ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }} text-xs px-2 py-1 rounded-full">
                        {{ $app->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <h3 class="text-white font-semibold mb-1">{{ $app->name }}</h3>
                <p class="text-gray-400 text-xs mb-4">{{ $app->description }}</p>
                <p class="text-gray-500 text-xs mb-4">{{ $app->companies_count }} {{ Str::plural('company', $app->companies_count) }} using this</p>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('apps.toggle', $app) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-yellow-400 hover:text-yellow-300 text-xs transition">
                            {{ $app->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('apps.destroy', $app) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Delete {{ $app->name }}?')"
                            class="text-red-400 hover:text-red-300 text-xs transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-3 bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
                <p class="text-gray-500 text-sm">No apps yet.</p>
            </div>
            @endforelse
        </div>
    </div>
</body>
</html>