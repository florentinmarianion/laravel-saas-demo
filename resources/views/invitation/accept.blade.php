<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accept Invitation — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-600 rounded-xl mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">You've been invited!</h1>
            <p class="text-gray-400 text-sm mt-1">
                Join <span class="text-white font-medium">{{ $invitation->company->name }}</span> 
                as <span class="text-green-400 font-medium">{{ $invitation->role }}</span>
            </p>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8">
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('invitation.accept', $invitation->token) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" value="{{ $invitation->email }}" disabled
                        class="w-full bg-gray-800/50 border border-gray-700 text-gray-400 rounded-lg px-4 py-3 text-sm">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-green-500 transition"
                        placeholder="John Doe">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-green-500 transition"
                        placeholder="••••••••">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-green-500 transition"
                        placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-500 text-white font-medium rounded-lg py-3 text-sm transition">
                    Create Account
                </button>
            </form>
        </div>
    </div>
</body>
</html>