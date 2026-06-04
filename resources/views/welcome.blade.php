<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Platform — Cloud Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-950 min-h-screen">

    {{-- Navbar --}}
    <nav class="border-b border-gray-800/50 px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-white font-bold">SaaS Platform</span>
            </div>
            <a href="{{ route('login') }}"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition">
                Sign In
            </a>
        </div>
    </nav>

    {{-- Hero --}}
    <div class="max-w-6xl mx-auto px-6 pt-20 pb-16 text-center">
        <div class="inline-flex items-center gap-2 bg-blue-600/10 border border-blue-600/20
            text-blue-400 text-xs px-3 py-1.5 rounded-full mb-6">
            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
            Multi-tenant Cloud Workspace
        </div>
        <h1 class="text-4xl sm:text-5xl font-bold text-white mb-5 leading-tight">
            One platform.<br>
            <span class="text-blue-400">All your business apps.</span>
        </h1>
        <p class="text-gray-400 text-lg max-w-xl mx-auto mb-8">
            Manage companies, users and business applications from a single workspace.
            Built for teams that need structure and flexibility.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('login') }}"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition">
                Get Started
            </a>
            <a href="#apps"
                class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-lg transition">
                See Apps
            </a>
        </div>
    </div>

    {{-- Features --}}
    <div class="max-w-6xl mx-auto px-6 pb-16">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-1">Multi-Company</h3>
                <p class="text-gray-400 text-sm">
                    One account, multiple workspaces. Switch between companies instantly.
                </p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="w-10 h-10 bg-green-600/20 rounded-lg flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-1">Granular Permissions</h3>
                <p class="text-gray-400 text-sm">
                    Control exactly what each user can do in each app, per company.
                </p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
                <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-semibold mb-1">Modular Apps</h3>
                <p class="text-gray-400 text-sm">
                    Enable only the apps each company needs. Each module is independent.
                </p>
            </div>
        </div>
    </div>

    {{-- Apps --}}
    <div id="apps" class="max-w-6xl mx-auto px-6 pb-20">
        <h2 class="text-white font-bold text-xl mb-6 text-center">Available Apps</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
            @foreach([
                ['name' => 'Currency Exchange', 'color' => 'blue',   'icon' => '💱', 'status' => 'live'],
                ['name' => 'Accounting',        'color' => 'yellow', 'icon' => '📊', 'status' => 'soon'],
                ['name' => 'HR Management',     'color' => 'green',  'icon' => '👥', 'status' => 'soon'],
                ['name' => 'Beauty Salon',      'color' => 'pink',   'icon' => '💅', 'status' => 'soon'],
                ['name' => 'Forex',             'color' => 'purple', 'icon' => '📈', 'status' => 'soon'],
            ] as $app)
            @php
                $colors = [
                    'blue'   => 'bg-blue-600/10 border-blue-600/20 text-blue-400',
                    'yellow' => 'bg-yellow-600/10 border-yellow-600/20 text-yellow-400',
                    'green'  => 'bg-green-600/10 border-green-600/20 text-green-400',
                    'pink'   => 'bg-pink-600/10 border-pink-600/20 text-pink-400',
                    'purple' => 'bg-purple-600/10 border-purple-600/20 text-purple-400',
                ];
                $c = $colors[$app['color']];
            @endphp
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
                <div class="text-2xl mb-2">{{ $app['icon'] }}</div>
                <p class="text-white text-xs font-medium mb-2">{{ $app['name'] }}</p>
                @if($app['status'] === 'live')
                    <span class="bg-green-500/10 text-green-400 text-xs px-2 py-0.5 rounded-full">Live</span>
                @else
                    <span class="bg-gray-800 text-gray-500 text-xs px-2 py-0.5 rounded-full">Soon</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-800 px-6 py-6">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <p class="text-gray-600 text-xs">© {{ date('Y') }} SaaS Platform</p>
            <a href="{{ route('login') }}" class="text-gray-500 hover:text-white text-xs transition">
                Sign In →
            </a>
        </div>
    </div>

</body>
</html>
