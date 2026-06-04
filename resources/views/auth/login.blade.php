<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-xl mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">SaaS Platform</h1>
            <p class="text-gray-400 text-sm mt-1">Sign in to your account</p>
        </div>

        @if(session('status'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3 mb-5">
            {{ session('status') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-5">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-xs font-medium mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition
                                placeholder-gray-600"
                            placeholder="you@example.com">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-gray-400 text-xs font-medium">Password</label>
                            <a href="{{ route('password.request') }}"
                                class="text-gray-500 hover:text-blue-400 text-xs transition">
                                Forgot password?
                            </a>
                        </div>
                        <input type="password" name="password" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                                px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-600">
                        <label for="remember" class="text-gray-400 text-sm cursor-pointer">
                            Remember me
                        </label>
                    </div>
                </div>
                <button type="submit"
                    class="w-full mt-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm
                        font-medium rounded-lg transition">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">
            Don't have an account? Contact your administrator for an invitation.
        </p>
    </div>
</body>
</html>
