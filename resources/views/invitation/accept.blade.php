<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-xl mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">You're invited!</h1>
            <p class="text-gray-400 text-sm mt-1">
                Join <span class="text-white font-medium">{{ $invitation->company->name }}</span>
                as <span class="text-blue-400">{{ $invitation->role }}</span>
            </p>
            <p class="text-gray-600 text-xs mt-1">{{ $invitation->email }}</p>
        </div>
        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-5">
            {{ $errors->first() }}
        </div>
        @endif
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('invitation.accept', $invitation->token) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-xs font-medium mb-1.5">Your Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                        @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-medium mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                        @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-medium mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition">
                    Create Account & Join
                </button>
            </form>
        </div>
        <p class="text-center text-gray-600 text-xs mt-4">
            Invitation expires {{ $invitation->expires_at->diffForHumans() }}
        </p>
    </div>
</body>
</html>
