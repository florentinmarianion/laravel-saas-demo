<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile — SaaS Platform</title>
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
                <span class="text-gray-600 text-sm">/ Profile</span>
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

    <div class="max-w-2xl mx-auto px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3">
                {{ session('success') }}
            </div>
        @endif

        <!-- Profile Info -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center">
                    <span class="text-blue-400 text-2xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
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

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                @if($errors->has('name') || $errors->has('email'))
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
            <h2 class="text-white font-semibold mb-6">Change Password</h2>

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PUT')

                @if($errors->has('current_password') || $errors->has('password'))
                    <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                    <input type="password" name="password" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>

                <button type="submit" class="bg-yellow-600 hover:bg-yellow-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</body>
</html>