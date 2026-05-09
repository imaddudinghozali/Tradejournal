<aside
    x-cloak
    :class="sidebarCollapsed ? 'w-24' : 'w-72'"
    class="hidden md:flex bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 border-r border-slate-800 min-h-screen p-4 md:p-5 flex-col transition-all duration-300"
>
    <div class="mb-6" :class="sidebarCollapsed ? 'text-center' : ''">
        <div class="text-cyan-300 font-bold text-2xl tracking-tight leading-none" x-text="sidebarCollapsed ? 'HRL' : 'HardRiskLedger'"></div>
        <div class="text-xs text-slate-500 mt-1" x-show="!sidebarCollapsed" x-transition.opacity>Trading Journal</div>
    </div>

    <nav class="space-y-1.5 text-sm">
        @php
            $items = [
                ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'DB'],
                ['route' => 'trading-accounts.*', 'label' => 'Trading Accounts', 'url' => route('trading-accounts.index'), 'icon' => 'AC'],
                ['route' => 'trades.*', 'label' => 'Trade Journal', 'url' => route('trades.index'), 'icon' => 'TR'],
                ['route' => 'risk-ledger.*', 'label' => 'Risk Ledger', 'url' => route('risk-ledger.index'), 'icon' => 'RK'],
                ['route' => 'prop-challenges.*', 'label' => 'Prop Challenges', 'url' => route('prop-challenges.index'), 'icon' => 'PC'],
                ['route' => 'amdx.*', 'label' => 'AMDX Journal', 'url' => route('amdx.index'), 'icon' => 'AX'],
                ['route' => 'statistics.*', 'label' => 'Statistics', 'url' => route('statistics.index'), 'icon' => 'ST'],
                ['route' => 'settings.*', 'label' => 'Settings', 'url' => route('settings.index'), 'icon' => 'SE'],
            ];
        @endphp

        @foreach($items as $item)
            @php
                $isDashboard = $item['route'] === 'dashboard';
                $active = $isDashboard ? request()->routeIs('dashboard') : request()->routeIs($item['route']);
                $href = $item['url'] ?? route('dashboard');
            @endphp
            <a href="{{ $href }}" :class="sidebarCollapsed ? 'justify-center' : ''" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ $active ? 'bg-gradient-to-r from-cyan-400/20 to-fuchsia-500/12 text-cyan-200 border border-cyan-300/20 shadow-[0_0_0_1px_rgba(34,211,238,0.08)]' : 'text-slate-300 hover:bg-slate-800/70 hover:text-slate-100 border border-transparent' }}">
                <span class="text-[10px] font-semibold opacity-80">{{ $item['icon'] }}</span>
                <span x-show="!sidebarCollapsed" x-transition.opacity>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-800 text-xs text-slate-400" :class="sidebarCollapsed ? 'text-center' : ''">
        <div class="font-medium text-slate-200" x-show="!sidebarCollapsed" x-transition.opacity>{{ auth()->user()->name }}</div>
        <div class="truncate" x-show="!sidebarCollapsed" x-transition.opacity>{{ auth()->user()->email }}</div>
        <div class="mt-3 flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : ''">
            <a href="{{ route('profile.edit') }}" class="text-cyan-300 hover:text-cyan-200">Profile</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-rose-400 hover:text-rose-300 font-semibold" title="Logout">Logout</button></form>
        </div>
    </div>
</aside>

<div x-cloak x-show="sidebarOpen" class="md:hidden fixed inset-0 z-40 bg-black/50" @click="sidebarOpen = false"></div>
<aside x-cloak x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="md:hidden fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 border-r border-slate-800 p-5">
    <div class="flex items-center justify-between mb-5">
        <div class="text-cyan-300 font-bold text-xl">HardRiskLedger</div>
        <button @click="sidebarOpen = false" class="text-slate-300" aria-label="Close menu" title="Close menu"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <nav class="space-y-2 text-sm">
        <a @click="sidebarOpen=false" href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">Dashboard</a>
        <a @click="sidebarOpen=false" href="{{ route('trading-accounts.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('trading-accounts.*') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">Trading Accounts</a>
        <a @click="sidebarOpen=false" href="{{ route('trades.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('trades.*') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">Trade Journal</a>
        <a @click="sidebarOpen=false" href="{{ route('risk-ledger.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('risk-ledger.*') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">Risk Ledger</a>
        <a @click="sidebarOpen=false" href="{{ route('prop-challenges.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('prop-challenges.*') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">Prop Challenges</a>
        <a @click="sidebarOpen=false" href="{{ route('amdx.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('amdx.*') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">AMDX Journal</a>
        <a @click="sidebarOpen=false" href="{{ route('statistics.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('statistics.*') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">Statistics</a>
        <a @click="sidebarOpen=false" href="{{ route('settings.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('settings.*') ? 'bg-cyan-400/20 text-cyan-200' : 'text-slate-300' }}">Settings</a>
    </nav>
    <div class="mt-6 pt-4 border-t border-slate-800">
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full py-2 rounded-lg border border-rose-500/40 text-rose-300">Logout</button></form>
    </div>
</aside>


