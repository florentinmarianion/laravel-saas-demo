<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — SaaS Demo</title>
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
                <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Users</a>
                <a href="{{ route('audit.index') }}" class="text-gray-400 hover:text-white text-sm transition ml-4">Audit Log</a>
                <span class="text-gray-600 text-sm">/ Dashboard</span>
            </div>
            <div class="flex items-center gap-4">
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

        <!-- Add Company Form -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-white font-semibold mb-4">Add Company</h2>
            
            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 text-sm rounded-lg p-3 mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-lg p-3 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

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

        <!-- Send Invitation Form -->
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
                <div class="mt-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>

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

        <!-- Companies Table -->
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-white font-semibold">Recent Companies</h2>
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
                            <a href="{{ route('companies.users', $company) }}" 
                            class="text-blue-400 hover:text-blue-300 transition">
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
                                <a href="{{ route('companies.edit', $company) }}"
                                    class="text-blue-400 hover:text-blue-300 text-xs transition">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('companies.destroy', $company) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Delete {{ $company->name }}?')"
                                        class="text-red-400 hover:text-red-300 text-xs transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">No companies found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Invitations Table -->
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
                            <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                                {{ $invitation->role }}
                            </span>
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
    </div>
</body>
</html>