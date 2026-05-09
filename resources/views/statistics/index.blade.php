<x-app-layout>
    <x-slot name="header"><h2 class="text-cyan-300 text-3xl font-semibold tracking-tight">Statistics</h2></x-slot>

    <div class="space-y-5">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5">
            <form method="GET" class="space-y-3">
                <div class="flex flex-wrap gap-2 text-xs">
                    @foreach(['today' => 'Today', 'weekly' => 'WTD', 'month' => 'MTD', '3months' => '3M', '1year' => '1Y', 'custom' => 'Custom'] as $key => $label)
                        <button name="period" value="{{ $key }}" class="px-3 py-1.5 rounded-full border {{ $period === $key ? 'bg-cyan-400 text-slate-950 border-cyan-300 shadow-[0_0_22px_rgba(34,211,238,.32)]' : 'bg-slate-900 text-slate-300 border-slate-700' }}">{{ $label }}</button>
                    @endforeach
                </div>
                <div class="grid md:grid-cols-5 gap-2">
                    <select name="account_id" class="bg-slate-950 border border-slate-700 rounded-lg p-2.5 md:col-span-2"><option value="">All Accounts</option>@foreach($accounts as $acc)<option value="{{ $acc->id }}" @selected((string)$selectedAccountId === (string)$acc->id)>{{ $acc->name }}</option>@endforeach</select>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="bg-slate-950 border border-slate-700 rounded-lg p-2.5">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="bg-slate-950 border border-slate-700 rounded-lg p-2.5">
                    <button class="bg-slate-900 border border-slate-700 rounded-lg p-2.5 hover:border-cyan-400/40">Apply</button>
                </div>
            </form>
            <div class="mt-3 flex justify-end">
                <a href="{{ route('statistics.export', request()->query()) }}" class="px-3 py-2 border border-slate-700 rounded-lg text-slate-200 text-sm">Export CSV</a>
            </div>
        </div>

        <div class="grid xl:grid-cols-2 gap-5">
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5"><h3 class="text-slate-100 font-semibold mb-3">P/L Bars</h3><div style="height:320px"><canvas id="plChart"></canvas></div></div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5"><h3 class="text-slate-100 font-semibold mb-3">Cumulative Curve</h3><div style="height:320px"><canvas id="cumChart"></canvas></div></div>
        </div>
        <p id="chartEmptyState" class="hidden text-sm text-slate-400 bg-slate-900/90 border border-slate-800 rounded-xl px-4 py-3">
            Belum ada data trade pada filter ini.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            const labels = @json($labels);
            const pl = @json($pl);
            if (!Array.isArray(labels) || !Array.isArray(pl) || labels.length === 0 || pl.length === 0) {
                document.getElementById('chartEmptyState')?.classList.remove('hidden');
                return;
            }

            const mode = document.body.getAttribute('data-neon') || 'medium';
            const posA = mode === 'soft' ? .6 : mode === 'strong' ? .92 : .78;
            const negA = mode === 'soft' ? .6 : mode === 'strong' ? .92 : .78;
            const colors = pl.map(v => Number(v) >= 0 ? `rgba(34,211,238,${posA})` : `rgba(244,114,182,${negA})`);
            const cum = pl.reduce((a, v, i) => { a.push((a[i-1] || 0) + Number(v)); return a; }, []);

            const barCtx = document.getElementById('plChart')?.getContext('2d');
            const lineCtx = document.getElementById('cumChart')?.getContext('2d');
            if (!barCtx || !lineCtx) return;

            new Chart(barCtx, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'P/L', data: pl, backgroundColor: colors, borderRadius: 8, maxBarThickness: 24, hoverBackgroundColor: colors }] },
                options: { maintainAspectRatio: false, animation: { duration: 800, easing: 'easeOutQuart' }, events: ['mousemove','mouseout','click','touchstart','touchmove'], plugins: { legend: { labels: { color: '#e2e8f0' } }, tooltip: { backgroundColor: 'rgba(2,6,23,.95)', borderColor: 'rgba(34,211,238,.45)', borderWidth: 1 } }, scales: { x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(34,211,238,.10)' } }, y: { ticks: { color: '#94a3b8', callback: (v) => '$' + Number(v).toLocaleString() }, grid: { color: (ctx) => ctx.tick.value === 0 ? 'rgba(56,189,248,.4)' : 'rgba(192,132,252,.10)' } } } }
            });

            const grad = lineCtx.createLinearGradient(0, 0, 0, 320);
            grad.addColorStop(0, `rgba(34,211,238,${mode === 'soft' ? .28 : mode === 'strong' ? .52 : .40})`);
            grad.addColorStop(.65, `rgba(192,132,252,${mode === 'soft' ? .1 : mode === 'strong' ? .22 : .14})`);
            grad.addColorStop(1, 'rgba(34,211,238,0)');
            const lineColor = mode === 'soft' ? '#67e8f9' : mode === 'strong' ? '#22d3ee' : '#38bdf8';

            new Chart(lineCtx, {
                type: 'line',
                data: { labels, datasets: [{ label: 'Cumulative', data: cum, borderColor: lineColor, backgroundColor: grad, fill: true, tension: .45, borderWidth: 3, pointRadius: 0, pointHitRadius: 24, pointHoverRadius: 6, pointHoverBackgroundColor: '#67e8f9' }] },
                options: { maintainAspectRatio: false, animation: { duration: 900, easing: 'easeOutQuart' }, events: ['mousemove','mouseout','click','touchstart','touchmove'], interaction: { mode: 'index', intersect: false }, plugins: { legend: { labels: { color: '#e2e8f0' } }, tooltip: { backgroundColor: 'rgba(2,6,23,.95)', borderColor: 'rgba(34,211,238,.45)', borderWidth: 1 } }, scales: { x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(34,211,238,.10)' } }, y: { ticks: { color: '#94a3b8', callback: (v) => '$' + Number(v).toLocaleString() }, grid: { color: 'rgba(192,132,252,.10)' } } } }
            });
        })();
    </script>
</x-app-layout>

