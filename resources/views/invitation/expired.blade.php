<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Expired — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-red-600/20 rounded-xl mb-4">
            <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white mb-2">Invitation Expired</h1>
        <p class="text-gray-400 text-sm mb-6">
            This invitation is no longer valid. Please contact your administrator for a new one.
        </p>
        <a href="{{ route('login') }}"
            class="inline-block px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
            Go to Login
        </a>
    </div>
</body>
</html>
