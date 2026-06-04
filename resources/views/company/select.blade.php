<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Company — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg px-6">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-xl mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Select Company</h1>
            <p class="text-gray-400 text-sm mt-1">
                Welcome, <span class="text-white">{{ Auth::user()->name }}</span>.
                Choose a workspace to continue.
            </p>
        </div>

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="space-y-3">
            @foreach($companies as $company)
            <form method="POST" action="{{ route('company.switch') }}">
                @csrf
                <input type="hidden" name="company_id" value="{{ $company->id }}">
                <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 border border-gray-800
                    hover:border-blue-500/50 rounded-xl p-5 text-left transition group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center
                                group-hover:bg-blue-600/30 transition">
                                <span class="text-blue-400 font-bold text-sm">
                                    {{ strtoupper(substr($company->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-white font-semibold">{{ $company->name }}</p>
                                <p class="text-gray-500 text-xs">{{ $company->email }}</p>
                                @if($company->pivot->role)
                                    <span class="inline-block bg-blue-600/20 text-blue-400 text-xs
                                        px-2 py-0.5 rounded-full mt-1">
                                        {{ $company->pivot->role }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-400 transition"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </button>
            </form>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-600 hover:text-gray-400 text-sm transition">
                    Sign out
                </button>
            </form>
        </div>
    </div>
</body>
</html>
