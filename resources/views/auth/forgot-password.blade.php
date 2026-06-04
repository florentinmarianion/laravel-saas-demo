<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-xl mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-gray-400 text-sm mt-1">
                Enter your email and we'll send you a reset link.
            </p>
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
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-400 text-xs font-medium mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg
                            px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition
                            placeholder-gray-600"
                        placeholder="you@example.com">
                </div>
                <button type="submit"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm
                        font-medium rounded-lg transition">
                    Send Reset Link
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
