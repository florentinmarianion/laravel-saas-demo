<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apps for {{ $company->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen">

    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-white font-semibold">SaaS Platform</span>
                <span class="text-gray-600 text-sm">/ {{ $company->name }}</span>
                <span class="text-gray-600 text-sm">/ Apps</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('apps.index') }}" class="text-gray-500 hover:text-white text-sm transition">← Apps</a>
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
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-800">
                <div class="w-12 h-12 bg-blue-600/20 rounded-xl flex items-center justify-center">
                    <span class="text-blue-400 text-xl font-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h1 class="text-white font-bold text-lg">{{ $company->name }}</h1>
                    <p class="text-gray-400 text-sm">Select which apps this company has access to</p>
                </div>
            </div>

            <form method="POST" action="{{ route('apps.company.sync', $company) }}">
                @csrf
                @method('PUT')

                @if($allApps->isEmpty())
                    <p class="text-gray-500 text-sm">No apps available. <a href="{{ route('apps.index') }}" class="text-blue-400">Create some first.</a></p>
                @else
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @foreach($allApps as $app)
                        @php
                            $colors = [
                                'blue'   => 'border-blue-600/30 bg-blue-600/10',
                                'green'  => 'border-green-600/30 bg-green-600/10',
                                'yellow' => 'border-yellow-600/30 bg-yellow-600/10',
                                'purple' => 'border-purple-600/30 bg-purple-600/10',
                                'pink'   => 'border-pink-600/30 bg-pink-600/10',
                                'red'    => 'border-red-600/30 bg-red-600/10',
                                'orange' => 'border-orange-600/30 bg-orange-600/10',
                            ];
                            $colorClass = $colors[$app->color] ?? $colors['blue'];
                            $checked = in_array($app->id, $companyAppIds);
                        @endphp
                        <label class="flex items-center gap-4 p-4 rounded-xl border {{ $checked ? $colorClass : 'border-gray-700 bg-gray-800/30' }} cursor-pointer hover:border-gray-600 transition">
                            <input type="checkbox" name="app_ids[]" value="{{ $app->id }}"
                                {{ $checked ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600">
                            <div>
                                <p class="text-white text-sm font-medium">{{ $app->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $app->description }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                            Save Apps
                        </button>
                        <a href="{{ route('apps.index') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium px-6 py-2.5 rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>
</body>
</html>