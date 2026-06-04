<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <p class="text-blue-400 text-sm font-semibold tracking-widest uppercase mb-4">404</p>
        <h1 class="text-4xl font-bold mb-3">Page not found</h1>
        <p class="text-gray-400 mb-8">The page you are looking for doesn't exist or you don't have access to it.</p>
        <a href="{{ url('/dashboard') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
            ← Back to Dashboard
        </a>
    </div>
</body>
</html>
