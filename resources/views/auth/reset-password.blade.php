<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-xl mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">New Password</h1>
            <p class="text-gray-400 text-sm mt-1">Choose a strong password for your account.</p>
        </div>

        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-5">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-xs font-medium mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                        @error('email')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-medium mb-1.5">New Password</label>
                        <input type="password" name="password" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                        @error('password')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-medium mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm
                        font-medium rounded-lg transition">
                    Reset Password
                </button>
            </form>
        </div>

        <p class="text-center mt-5">
            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-300 text-sm transition">
                ← Back to login
            </a>
        </p>
    </div>
</body>
</html>
