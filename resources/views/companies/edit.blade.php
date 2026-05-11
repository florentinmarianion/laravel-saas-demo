<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $company->name }}</title>
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
                <span class="text-gray-600 text-sm">/ Dashboard</span>
                <span class="text-gray-600 text-sm">/</span>
                <span class="text-gray-400 text-sm">Edit Company</span>
            </div>
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-white text-sm transition">
                ← Back to Dashboard
            </a>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-6 py-8">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-8">
            <h1 class="text-white font-bold text-lg mb-6">Edit Company</h1>

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('companies.update', $company) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Company Name</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" required
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="mb-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" 
                            {{ $company->is_active ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-700 bg-gray-800 text-blue-600">
                        <span class="text-sm font-medium text-gray-300">Active</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                        Save Changes
                    </button>
                    <a href="{{ route('dashboard') }}" class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-sm font-medium px-6 py-2.5 rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>