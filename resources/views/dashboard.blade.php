<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — SaaS Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 min-h-screen">

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
                @role('admin')
                    <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Users</a>
                    <a href="{{ route('apps.index') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Apps</a>
                    <a href="{{ route('audit.index') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Audit Log</a>
                    <a href="{{ route('permissions.index') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Permissions</a>
                @endrole
                @canany(['companies.read', 'users.read', 'currency.view'])
                    @unlessrole('admin')
                        @can('companies.read')
                            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Companies</a>
                        @endcan
                        @can('users.read')
                            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Users</a>
                        @endcan
                        @can('currency.view')
                            <a href="#" class="text-gray-400 hover:text-white text-sm transition ml-4">Currency Exchange</a>
                        @endcan
                    @endunlessrole
                @endcanany
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    @php $unread = Auth::user()->unreadNotifications->count(); @endphp
                    <a href="{{ route('notifications.index') }}" class="relative text-gray-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-2.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center">
                                {{ $unread }}
                            </span>
                        @endif
                    </a>
                </div>
                <a href="{{ route('profile.show') }}" class="text-gray-400 hover:text-white text-sm transition">
                    {{ Auth::user()->name }}
                </a>
                <span class="bg-blue-600/20 text-blue-400 text-xs font-medium px-2 py-1 rounded-full">
                    {{ Auth::user()->getRoleNames()->first() ?? 'user' }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-gray-500 hover:text-white text-sm transition">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-6 py-8">

        @if(session('success'))
            <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3 mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        @role('admin')

        <!-- Add Company Form -->
        @can('companies.create')
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-white font-semibold mb-4">Add Company</h2>
            <form method="POST" action="{{ route('companies.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Company Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition"
                            placeholder="Acme Corp">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition"
                            placeholder="contact@acme.com">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        Create Company
                    </button>
                </div>
            </form>
        </div>
        @endcan

        <!-- Send Invitation Form -->
        @can('invitations.create')
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-white font-semibold mb-4">Send Invitation</h2>
            <form method="POST" action="{{ route('invitations.send') }}">
                @csrf
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition"
                            placeholder="user@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Company</label>
                        <select name="company_id" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                            <option value="">Select company...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                        <select name="role" required
                            class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500 transition">
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                @if($allPermissions->isNotEmpty())
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-3">Permissions <span class="text-gray-500 font-normal">(optional)</span></label>
                    @php
                        $grouped = $allPermissions->groupBy(function($p) {
                            return explode('.', $p->name)[0];
                        });
                    @endphp
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($grouped as $module => $permissions)
                        <div class="bg-gray-800/50 rounded-lg p-3">
                            <p class="text-gray-400 text-xs uppercase tracking-wider mb-2">{{ $module }}</p>
                            @foreach($permissions as $permission)
                            <label class="flex items-center gap-2 cursor-pointer mb-1">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    class="w-3.5 h-3.5 rounded border-gray-600 bg-gray-700 text-blue-600">
                                <span class="text-gray-300 text-xs font-mono">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="mt-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
        @endcan

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <p class="text-gray-400 text-sm mb-1">Companies</p>
                <p class="text-3xl font-bold text-white">{{ $stats['companies'] }}</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <p class="text-gray-400 text-sm mb-1">Users</p>
                <p class="text-3xl font-bold text-white">{{ $stats['users'] }}</p>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <p class="text-gray-400 text-sm mb-1">Pending Invitations</p>
                <p class="text-3xl font-bold text-white">{{ $stats['invitations'] }}</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-white font-semibold mb-4">Companies — Last 30 Days</h3>
                <canvas id="companiesChart" height="120"></canvas>
            </div>
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                <h3 class="text-white font-semibold mb-4">Users — Last 30 Days</h3>
                <canvas id="usersChart" height="120"></canvas>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const labels = @json($dateLabels);
            const companiesData = @json($companiesData);
            const usersData = @json($usersData);
            const chartOptions = {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#6b7280', maxTicksLimit: 8, font: { size: 11 } }, grid: { color: '#1f2937' } },
                    y: { ticks: { color: '#6b7280', font: { size: 11 }, stepSize: 1 }, grid: { color: '#1f2937' }, beginAtZero: true }
                }
            };
            new Chart(document.getElementById('companiesChart'), {
                type: 'line',
                data: { labels, datasets: [{ data: companiesData, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#3b82f6' }] },
                options: chartOptions
            });
            new Chart(document.getElementById('usersChart'), {
                type: 'line',
                data: { labels, datasets: [{ data: usersData, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#10b981' }] },
                options: chartOptions
            });
        </script>

        <!-- Companies Table -->
        @can('companies.read')
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <h2 class="text-white font-semibold">Recent Companies</h2>
                <a href="{{ route('export.companies') }}"
                    class="bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs font-medium px-3 py-1.5 rounded-lg transition flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Company</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Email</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Users</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Status</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center">
                                    <span class="text-blue-400 text-xs font-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-white text-sm font-medium">{{ $company->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $company->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $company->email }}</td>
                        <td class="px-6 py-4 text-gray-400 text-sm">
                            <a href="{{ route('companies.users', $company) }}" class="text-blue-400 hover:text-blue-300 transition">
                                {{ $company->users->count() }} users
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            @if($company->is_active)
                                <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                            @else
                                <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @can('companies.update')
                                <a href="{{ route('companies.edit', $company) }}" class="text-blue-400 hover:text-blue-300 text-xs transition">Edit</a>
                                @endcan
                                <a href="{{ route('apps.company', $company) }}" class="text-purple-400 hover:text-purple-300 text-xs transition">Apps</a>
                                @can('companies.delete')
                                <form method="POST" action="{{ route('companies.destroy', $company) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Delete {{ $company->name }}?')"
                                        class="text-red-400 hover:text-red-300 text-xs transition">
                                        Delete
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No companies found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endcan

        <!-- Invitations Table -->
        @can('invitations.create')
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-white font-semibold">Invitations</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Email</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Company</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Role</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Expires</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Status</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invitations as $invitation)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4 text-white text-sm">{{ $invitation->email }}</td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $invitation->company->name }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">{{ $invitation->role }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $invitation->expires_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            @if($invitation->accepted_at)
                                <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Accepted</span>
                            @elseif($invitation->expires_at->isPast())
                                <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Expired</span>
                            @else
                                <span class="bg-yellow-500/10 text-yellow-400 text-xs px-2 py-1 rounded-full">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @can('invitations.delete')
                            @if($invitation->isPending())
                            <form method="POST" action="{{ route('invitations.destroy', $invitation) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Cancel this invitation?')"
                                    class="text-red-400 hover:text-red-300 text-xs transition">
                                    Cancel
                                </button>
                            </form>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">No invitations found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endcan

        @endrole

        {{-- Member view --}}
        @unlessrole('admin')
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-12 text-center mb-6">
            <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="text-white font-semibold text-lg mb-2">Welcome, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-400 text-sm mb-6">You are logged in as <span class="text-blue-400">{{ Auth::user()->getRoleNames()->first() ?? 'member' }}</span> at <span class="text-white">{{ Auth::user()->company?->name ?? 'N/A' }}</span>.</p>
            @cannot('currency.view')
                <p class="text-gray-500 text-xs">Contact your administrator to get access to modules.</p>
            @endcannot
        </div>

        {{-- Member: Companies table (read only) --}}
        @can('companies.read')
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-white font-semibold">Companies</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Company</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Email</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Status</th>
                        @canany(['companies.update', 'companies.delete'])
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-600/20 rounded-lg flex items-center justify-center">
                                    <span class="text-blue-400 text-xs font-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-white text-sm font-medium">{{ $company->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $company->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $company->email }}</td>
                        <td class="px-6 py-4">
                            @if($company->is_active)
                                <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                            @else
                                <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        @canany(['companies.update', 'companies.delete'])
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @can('companies.update')
                                <a href="{{ route('companies.edit', $company) }}" class="text-blue-400 hover:text-blue-300 text-xs transition">Edit</a>
                                @endcan
                                @can('companies.delete')
                                <form method="POST" action="{{ route('companies.destroy', $company) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Delete {{ $company->name }}?')"
                                        class="text-red-400 hover:text-red-300 text-xs transition">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                        @endcanany
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">No companies found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endcan

        {{-- Member: Users table --}}
        @can('users.read')
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-white font-semibold">Users</h2>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Name</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Email</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Role</th>
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Status</th>
                        @canany(['users.update', 'users.delete'])
                        <th class="text-left text-gray-400 text-xs font-medium px-6 py-3">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                    <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                        <td class="px-6 py-4 text-white text-sm">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                                {{ $user->roles->first()?->name ?? 'no role' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                            @else
                                <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        @canany(['users.update', 'users.delete'])
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @can('users.delete')
                                <form method="POST" action="{{ route('users.destroy', $user) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Delete {{ $user->name }}?')"
                                        class="text-red-400 hover:text-red-300 text-xs transition">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                        @endcanany
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endcan

        @endunlessrole
    </div>
</body>
</html>