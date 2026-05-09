<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h2 class="font-semibold text-3xl tracking-tight text-slate-100">Trading Command Center</h2>
                <p class="text-sm text-cyan-300/80 mt-1">Account: <span class="text-fuchsia-300">{{ $selectedAccountName }}</span></p>
            </div>
            <form method="GET" class="w-full xl:w-auto space-y-2">
                <div class="flex flex-wrap gap-2 text-xs">
                    @foreach(['today' => 'Today', 'weekly' => 'WTD', 'month' => 'MTD', '3months' => '3M', '1year' => '1Y', 'custom' => 'Custom'] as $key => $label)
                        <button name="period" value="{{ $key }}" class="px-3 py-1.5 rounded-full border transition {{ $period === $key ? 'bg-cyan-400 text-slate-950 border-cyan-300 shadow-[0_0_22px_rgba(34,211,238,.32)]' : 'bg-slate-900/80 text-slate-300 border-slate-700 hover:border-cyan-500/60' }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                    <select name="account_id" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-slate-300 md:col-span-2"><option value="">All Accounts</option>@foreach($accounts as $acc)<option value="{{ $acc->id }}" @selected((string)$selectedAccountId === (string)$acc->id)>{{ $acc->name }}</option>@endforeach</select>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-slate-300">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-slate-300">
                </div>
                <button class="px-4 py-2 rounded-lg border border-slate-700 bg-slate-900 text-slate-300 text-xs hover:border-cyan-400/60">Apply Filter</button>
            </form>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-cyan-900/40 bg-gradient-to-br from-slate-900 to-slate-950 p-5 shadow-xl shadow-cyan-950/20"><p class="text-xs uppercase tracking-wide text-slate-500">Net P/L</p><p class="mt-3 text-3xl font-semibold {{ $periodPL >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">${{ number_format($periodPL, 2) }}</p></div>
            <div class="rounded-2xl border border-fuchsia-900/40 bg-gradient-to-br from-slate-900 to-slate-950 p-5 shadow-xl shadow-fuchsia-950/20"><p class="text-xs uppercase tracking-wide text-slate-500">Win Rate</p><p class="mt-3 text-3xl font-semibold text-slate-100">{{ $winrate }}%</p><p class="text-xs mt-2 text-slate-500">{{ $wins }}W / {{ $losses }}L</p></div>
            <div class="rounded-2xl border border-cyan-900/40 bg-gradient-to-br from-slate-900 to-slate-950 p-5 shadow-xl shadow-cyan-950/20"><p class="text-xs uppercase tracking-wide text-slate-500">Daily DD Left</p><p class="mt-3 text-3xl font-semibold text-cyan-300">${{ number_format($remainingDaily, 2) }}</p></div>
            <div class="rounded-2xl border border-fuchsia-900/40 bg-gradient-to-br from-slate-900 to-slate-950 p-5 shadow-xl shadow-fuchsia-950/20"><p class="text-xs uppercase tracking-wide text-slate-500">Max Drawdown</p><p class="mt-3 text-3xl font-semibold text-fuchsia-300">${{ number_format($maxDrawdown, 2) }}</p></div>
        </div>

        <div class="grid gap-5 xl:grid-cols-3">
            <div class="xl:col-span-2 rounded-2xl border border-slate-800 bg-slate-900/90 p-5"><div class="flex items-center justify-between mb-4"><h3 class="text-base font-semibold text-slate-100">Equity Curve</h3><span class="text-xs px-2.5 py-1 rounded-full {{ $status === 'safe' ? 'bg-emerald-500/20 text-emerald-300' : ($status === 'warning' ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">{{ strtoupper($status) }}</span></div><div style="height:340px"><canvas id="equityCurve"></canvas></div></div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/90 p-5"><h3 class="text-base font-semibold text-slate-100 mb-4">Risk Snapshot</h3><div class="space-y-3.5 text-sm"><div class="flex justify-between"><span class="text-slate-400">Total Trades</span><span class="text-slate-100">{{ $total }}</span></div><div class="flex justify-between"><span class="text-slate-400">Period P/L</span><span class="{{ $periodPL >= 0 ? 'text-emerald-400':'text-rose-400' }}">${{ number_format($periodPL, 2) }}</span></div><div class="flex justify-between"><span class="text-slate-400">Win Rate</span><span class="text-slate-100">{{ $winrate }}%</span></div><div class="flex justify-between"><span class="text-slate-400">Remaining Max DD</span><span class="text-cyan-300">${{ number_format($remainingMax, 2) }}</span></div></div></div>
        </div>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            const canvas = document.getElementById('equityCurve');
            if (!canvas) return;
            const labels = @json($curveLabels);
            const values = @json($curveData);
            const ctx = canvas.getContext('2d');

            if (!Array.isArray(labels) || !Array.isArray(values) || labels.length === 0 || values.length === 0) {
                const p = document.createElement('p');
                p.className = 'text-slate-500 text-sm';
                p.textContent = 'Belum ada data curve untuk periode ini.';
                canvas.parentElement.replaceChildren(p);
                return;
            }

            const mode = document.body.getAttribute('data-neon') || 'medium';
            const alphaTop = mode === 'soft' ? .26 : mode === 'strong' ? .56 : .42;
            const alphaMid = mode === 'soft' ? .10 : mode === 'strong' ? .24 : .16;
            const lineColor = mode === 'soft' ? '#67e8f9' : mode === 'strong' ? '#22d3ee' : '#38bdf8';

            const grad = ctx.createLinearGradient(0, 0, 0, 340);
            grad.addColorStop(0, `rgba(34,211,238,${alphaTop})`);
            grad.addColorStop(.55, `rgba(192,132,252,${alphaMid})`);
            grad.addColorStop(1, 'rgba(34,211,238,0)');

            new Chart(ctx, {
                type: 'line',
                data: { labels, datasets: [{ label: 'Equity', data: values, borderColor: lineColor, backgroundColor: grad, tension: .48, fill: true, borderWidth: 3, pointRadius: 0, pointHitRadius: 26, pointHoverRadius: 6, pointHoverBorderWidth: 2, pointHoverBackgroundColor: '#67e8f9', pointHoverBorderColor: '#0f172a' }] },
                options: {
                    maintainAspectRatio: false,
                    animation: { duration: 900, easing: 'easeOutQuart' },
                    events: ['mousemove','mouseout','click','touchstart','touchmove'],
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { labels: { color: '#cbd5e1' } }, tooltip: { backgroundColor: 'rgba(2,6,23,.95)', borderColor: 'rgba(34,211,238,.45)', borderWidth: 1, titleColor: '#e2e8f0', bodyColor: '#bae6fd', callbacks: { label: (c) => `Equity: $${Number(c.raw).toLocaleString()}` } } },
                    scales: { x: { ticks: { color: '#94a3b8', maxRotation: 0 }, grid: { color: 'rgba(34,211,238,0.10)' } }, y: { ticks: { color: '#94a3b8', callback: (v) => '$' + Number(v).toLocaleString() }, grid: { color: 'rgba(192,132,252,0.10)' } } }
                }
            });
        })();
    </script>
</x-app-layout>

