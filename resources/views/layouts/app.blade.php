<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — SaaS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-950 min-h-full" x-data="{ mobileOpen: false }">

{{-- ═══════════════════════════════════════════════════════════════════════════
     TOP NAVBAR
═══════════════════════════════════════════════════════════════════════════ --}}
<nav class="bg-gray-900 border-b border-gray-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">

            {{-- LEFT: Logo + Desktop Nav --}}
            <div class="flex items-center gap-6">

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-7 h-7 bg-blue-600 rounded-md flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-sm hidden sm:block">SaaS Platform</span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center gap-1">

                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                        class="px-3 py-1.5 rounded-md text-sm transition
                            {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        Dashboard
                    </a>

                    @role('admin')

                    {{-- Users dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-md text-sm transition
                                {{ request()->routeIs('users.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Users
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-180'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute top-full left-0 mt-1 w-52 bg-gray-900 border border-gray-700 rounded-xl shadow-xl py-1 z-50">
                            <a href="{{ route('users.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                                </svg>
                                All Users
                            </a>
                            <a href="{{ route('users.index') }}#invite"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Invite User
                            </a>
                            <div class="border-t border-gray-800 my-1"></div>
                            <a href="{{ route('export.users') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export CSV
                            </a>
                        </div>
                    </div>

                    {{-- Companies dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-md text-sm transition
                                {{ request()->routeIs('companies.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Companies
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-180'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute top-full left-0 mt-1 w-52 bg-gray-900 border border-gray-700 rounded-xl shadow-xl py-1 z-50">
                            <a href="{{ route('companies.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                                All Companies
                            </a>
                            <a href="{{ route('export.companies') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Export CSV
                            </a>
                        </div>
                    </div>

                    {{-- Apps dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-md text-sm transition
                                {{ request()->routeIs('apps.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Apps
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-180'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute top-full left-0 mt-1 w-56 bg-gray-900 border border-gray-700 rounded-xl shadow-xl py-1 z-50">
                            <a href="{{ route('apps.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                                Manage Apps
                            </a>
                        </div>
                    </div>

                    {{-- Permissions dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @keydown.escape="open = false"
                            class="flex items-center gap-1 px-3 py-1.5 rounded-md text-sm transition
                                {{ request()->routeIs('permissions.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                            Permissions
                            <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-180'"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute top-full left-0 mt-1 w-56 bg-gray-900 border border-gray-700 rounded-xl shadow-xl py-1 z-50">
                            <a href="{{ route('permissions.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                Global Permissions
                            </a>
                            <a href="{{ route('audit.index') }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Audit Log
                            </a>
                        </div>
                    </div>

                    @endrole
                </div>
            </div>

            {{-- RIGHT: Company Switcher + Notifications + User --}}
            <div class="flex items-center gap-2">

                {{-- Company Switcher (non-admin users or admin with context) --}}
                @php $companies = \App\Services\AppContext::userCompanies(); @endphp
                @if($companies->count() > 0 && !\App\Services\AppContext::isAdmin())
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @keydown.escape="open = false"
                        class="flex items-center gap-2 px-3 py-1.5 bg-gray-800 hover:bg-gray-700
                            border border-gray-700 rounded-lg text-sm transition max-w-[160px]">
                        <div class="w-4 h-4 bg-blue-600/30 rounded flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-400 text-xs font-bold leading-none">
                                {{ strtoupper(substr(\App\Services\AppContext::companyName(), 0, 1)) }}
                            </span>
                        </div>
                        <span class="text-gray-200 truncate text-xs">
                            {{ \App\Services\AppContext::companyName() }}
                        </span>
                        <svg class="w-3 h-3 text-gray-500 flex-shrink-0 transition-transform" :class="open && 'rotate-180'"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute top-full right-0 mt-1 w-56 bg-gray-900 border border-gray-700 rounded-xl shadow-xl py-1 z-50">
                        <p class="px-4 py-2 text-xs text-gray-500 uppercase tracking-wider font-medium">Switch Company</p>
                        @foreach($companies as $co)
                        <form method="POST" action="{{ route('company.switch') }}">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $co->id }}">
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2 text-sm transition
                                    {{ $co->id == \App\Services\AppContext::companyId()
                                        ? 'text-blue-400 bg-blue-600/10'
                                        : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                                <div class="w-6 h-6 bg-gray-800 rounded flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold">{{ strtoupper(substr($co->name, 0, 2)) }}</span>
                                </div>
                                <div class="text-left">
                                    <p class="truncate">{{ $co->name }}</p>
                                    @if($co->pivot?->role)
                                        <p class="text-xs text-gray-500">{{ $co->pivot->role }}</p>
                                    @endif
                                </div>
                                @if($co->id == \App\Services\AppContext::companyId())
                                    <svg class="w-4 h-4 ml-auto text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Notifications Bell --}}
                @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                <a href="{{ route('notifications.index') }}"
                    class="relative p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-2.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unread > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs
                            rounded-full flex items-center justify-center font-medium">
                            {{ $unread > 9 ? '9+' : $unread }}
                        </span>
                    @endif
                </a>

                {{-- User Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @keydown.escape="open = false"
                        class="flex items-center gap-2 px-2 py-1.5 hover:bg-gray-800 rounded-lg transition">
                        <div class="w-7 h-7 bg-blue-600/20 rounded-full flex items-center justify-center">
                            <span class="text-blue-400 text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-white text-xs font-medium leading-tight max-w-[100px] truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-gray-500 text-xs leading-tight">
                                {{ auth()->user()->getRoleNames()->first() ?? 'user' }}
                            </p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-500 hidden sm:block transition-transform"
                            :class="open && 'rotate-180'"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute top-full right-0 mt-1 w-48 bg-gray-900 border border-gray-700 rounded-xl shadow-xl py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-800">
                            <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                            <p class="text-gray-500 text-xs truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.show') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile
                        </a>
                        <div class="border-t border-gray-800 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-gray-800 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mobile Hamburger --}}
                <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-transition class="md:hidden border-t border-gray-800 py-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                Dashboard
            </a>
            @role('admin')
            <a href="{{ route('users.index') }}"
                class="block px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                Users
            </a>
            <a href="{{ route('apps.index') }}"
                class="block px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                Apps
            </a>
            <a href="{{ route('permissions.index') }}"
                class="block px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                Permissions
            </a>
            <a href="{{ route('audit.index') }}"
                class="block px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                Audit Log
            </a>
            @endrole
            <a href="{{ route('profile.show') }}"
                class="block px-3 py-2 rounded-md text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition">
                Profile
            </a>
        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════════════════════════════════════
     BREADCRUMBS
═══════════════════════════════════════════════════════════════════════════ --}}
@hasSection('breadcrumbs')
<div class="bg-gray-900/50 border-b border-gray-800/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
        <nav class="flex items-center gap-1.5 text-xs text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-white transition">Dashboard</a>
            @yield('breadcrumbs')
        </nav>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════
     FLASH MESSAGES
═══════════════════════════════════════════════════════════════════════════ --}}
@if(session('success') || $errors->any())
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3">
            {{ $errors->first() }}
        </div>
    @endif
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════
     MAIN CONTENT
═══════════════════════════════════════════════════════════════════════════ --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
