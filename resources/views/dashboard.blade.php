@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@role('admin')
{{-- ═══ STATS ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <p class="text-gray-400 text-sm">Companies</p>
        <p class="text-3xl font-bold text-white mt-1">{{ $stats['companies'] }}</p>
    </div>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <p class="text-gray-400 text-sm">Users</p>
        <p class="text-3xl font-bold text-white mt-1">{{ $stats['users'] }}</p>
    </div>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <p class="text-gray-400 text-sm">Pending Invitations</p>
        <p class="text-3xl font-bold text-white mt-1">{{ $stats['invitations'] }}</p>
    </div>
</div>

{{-- ═══ CHARTS ═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <h3 class="text-white font-semibold mb-4 text-sm">Companies — Last 30 Days</h3>
        <canvas id="companiesChart" height="120"></canvas>
    </div>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-5">
        <h3 class="text-white font-semibold mb-4 text-sm">Users — Last 30 Days</h3>
        <canvas id="usersChart" height="120"></canvas>
    </div>
</div>

{{-- ═══ ADD COMPANY ══════════════════════════════════════════════════════════ --}}
@can('companies.create')
<div class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-4">
    <h2 class="text-white font-semibold mb-4 text-sm">Add Company</h2>
    <form method="POST" action="{{ route('companies.store') }}"
        class="flex flex-col sm:flex-row gap-3">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" required
            placeholder="Company name"
            class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5
                text-sm focus:outline-none focus:border-blue-500 transition">
        <input type="email" name="email" value="{{ old('email') }}" required
            placeholder="contact@company.com"
            class="flex-1 bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5
                text-sm focus:outline-none focus:border-blue-500 transition">
        <button type="submit"
            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition">
            Create
        </button>
    </form>
</div>
@endcan

{{-- ═══ INVITE USER ══════════════════════════════════════════════════════════ --}}
@can('invitations.create')
<div id="invite" class="bg-gray-900 border border-gray-800 rounded-xl p-5 mb-4">
    <h2 class="text-white font-semibold mb-4 text-sm">Send Invitation</h2>
    <form method="POST" action="{{ route('invitations.send') }}">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
            <input type="email" name="email" value="{{ old('email') }}" required
                placeholder="user@example.com"
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5
                    text-sm focus:outline-none focus:border-blue-500 transition">
            <select name="company_id" required
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5
                    text-sm focus:outline-none focus:border-blue-500 transition">
                <option value="">Select company...</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
            <select name="role" required
                class="bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2.5
                    text-sm focus:outline-none focus:border-blue-500 transition">
                <option value="member">Member</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        @if($allPermissions->isNotEmpty())
        <div class="mb-3">
            <p class="text-gray-400 text-xs mb-2">Permissions <span class="text-gray-600">(optional)</span></p>
            @php $grouped = $allPermissions->groupBy(fn($p) => explode('.', $p->name)[0]); @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach($grouped as $module => $permissions)
                <div class="bg-gray-800/50 rounded-lg p-2">
                    <p class="text-gray-500 text-xs uppercase tracking-wider mb-1">{{ $module }}</p>
                    @foreach($permissions as $permission)
                    <label class="flex items-center gap-1.5 cursor-pointer mb-0.5">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                            class="w-3 h-3 rounded border-gray-600 bg-gray-700 text-blue-600">
                        <span class="text-gray-300 text-xs font-mono">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <button type="submit"
            class="px-5 py-2.5 bg-green-600 hover:bg-green-500 text-white text-sm rounded-lg transition">
            Send Invitation
        </button>
    </form>
</div>
@endcan

{{-- ═══ COMPANIES TABLE ══════════════════════════════════════════════════════ --}}
@can('companies.read')
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-visible mb-4">
    <div class="px-5 py-3.5 border-b border-gray-800 flex items-center justify-between">
        <h2 class="text-white font-semibold text-sm">Companies</h2>
        <a href="{{ route('export.companies') }}"
            class="text-gray-500 hover:text-gray-300 text-xs transition flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
        </a>
    </div>
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-800">
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Company</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">Email</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Users</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden md:table-cell">Apps</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden lg:table-cell">Status</th>
                <th class="text-right text-gray-400 text-xs font-medium px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($companies as $company)
            <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-blue-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-blue-400 text-xs font-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-white text-sm font-medium">{{ $company->name }}</p>
                            <p class="text-gray-600 text-xs">{{ $company->slug }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3 hidden sm:table-cell text-gray-400 text-sm">{{ $company->email }}</td>
                <td class="px-5 py-3">
                    <a href="{{ route('companies.users', $company) }}"
                        class="text-blue-400 hover:text-blue-300 text-sm transition">
                        {{ $company->users->count() }} users
                    </a>
                </td>
                <td class="px-5 py-3 hidden md:table-cell">
                    <a href="{{ route('apps.company', $company) }}"
                        class="text-purple-400 hover:text-purple-300 text-sm transition">
                        {{ $company->apps->count() }} apps
                    </a>
                </td>
                <td class="px-5 py-3 hidden lg:table-cell">
                    @if($company->is_active)
                        <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Active</span>
                    @else
                        <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Inactive</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-end gap-2">
                        @can('companies.update')
                        <a href="{{ route('companies.edit', $company) }}"
                            class="text-gray-400 hover:text-white text-xs transition">Edit</a>
                        @endcan
                        @can('companies.delete')
                        <form method="POST" action="{{ route('companies.destroy', $company) }}"
                            onsubmit="return confirm('Delete {{ $company->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition">Delete</button>
                        </form>
                        @endcan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-500 text-sm">No companies yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endcan

{{-- ═══ INVITATIONS TABLE ════════════════════════════════════════════════════ --}}
@can('invitations.create')
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-visible">
    <div class="px-5 py-3.5 border-b border-gray-800">
        <h2 class="text-white font-semibold text-sm">Invitations</h2>
    </div>
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-800">
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Email</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden sm:table-cell">Company</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden md:table-cell">Role</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3 hidden lg:table-cell">Expires</th>
                <th class="text-left text-gray-400 text-xs font-medium px-5 py-3">Status</th>
                <th class="text-right text-gray-400 text-xs font-medium px-5 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invitations as $invitation)
            <tr class="border-b border-gray-800/50 hover:bg-gray-800/30 transition">
                <td class="px-5 py-3 text-white text-sm">{{ $invitation->email }}</td>
                <td class="px-5 py-3 hidden sm:table-cell">
                    <a href="{{ route('companies.users', $invitation->company) }}"
                        class="text-gray-400 hover:text-white text-sm transition">
                        {{ $invitation->company->name }}
                    </a>
                </td>
                <td class="px-5 py-3 hidden md:table-cell">
                    <span class="bg-blue-600/20 text-blue-400 text-xs px-2 py-1 rounded-full">
                        {{ $invitation->role }}
                    </span>
                </td>
                <td class="px-5 py-3 hidden lg:table-cell text-gray-400 text-sm">
                    {{ $invitation->expires_at->format('M d, Y') }}
                </td>
                <td class="px-5 py-3">
                    @if($invitation->accepted_at)
                        <span class="bg-green-500/10 text-green-400 text-xs px-2 py-1 rounded-full">Accepted</span>
                    @elseif($invitation->expires_at->isPast())
                        <span class="bg-red-500/10 text-red-400 text-xs px-2 py-1 rounded-full">Expired</span>
                    @else
                        <span class="bg-yellow-500/10 text-yellow-400 text-xs px-2 py-1 rounded-full">Pending</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-right">
                    @can('invitations.delete')
                    @if($invitation->isPending())
                    <form method="POST" action="{{ route('invitations.destroy', $invitation) }}"
                        onsubmit="return confirm('Cancel this invitation?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition">Cancel</button>
                    </form>
                    @endif
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-500 text-sm">No invitations yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endcan

@endrole

{{-- ═══ MEMBER VIEW ══════════════════════════════════════════════════════════ --}}
@unlessrole('admin')
<div class="bg-gray-900 border border-gray-800 rounded-xl p-10 text-center">
    <div class="w-14 h-14 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
        <span class="text-blue-400 text-2xl font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
    </div>
    <h2 class="text-white font-semibold text-lg mb-1">Welcome, {{ auth()->user()->name }}</h2>
    <p class="text-gray-400 text-sm">
        You are logged in as
        <span class="text-blue-400">{{ auth()->user()->getRoleNames()->first() ?? 'member' }}</span>
        @if(session('active_company_name'))
            at <span class="text-white">{{ session('active_company_name') }}</span>
        @endif
    </p>
</div>
@endunlessrole

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($dateLabels);
const opts = {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
        x: { ticks: { color: '#6b7280', maxTicksLimit: 8, font: { size: 11 } }, grid: { color: '#1f2937' } },
        y: { ticks: { color: '#6b7280', font: { size: 11 }, stepSize: 1 }, grid: { color: '#1f2937' }, beginAtZero: true }
    }
};
new Chart(document.getElementById('companiesChart'), {
    type: 'line',
    data: { labels, datasets: [{ data: @json($companiesData), borderColor: '#3b82f6',
        backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.4, pointRadius: 3 }] },
    options: opts
});
new Chart(document.getElementById('usersChart'), {
    type: 'line',
    data: { labels, datasets: [{ data: @json($usersData), borderColor: '#10b981',
        backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.4, pointRadius: 3 }] },
    options: opts
});
</script>
@endpush
