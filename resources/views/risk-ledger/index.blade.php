<x-app-layout>
    <x-slot name="header"><h2 class="text-cyan-300 text-3xl font-semibold tracking-tight">Risk Ledger</h2></x-slot>

    <div class="space-y-4">
        <form method="GET" class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-4 grid md:grid-cols-4 gap-2">
            <select name="account_id" class="bg-slate-950 border border-slate-700 rounded p-2 md:col-span-3">
                <option value="">All Accounts</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" @selected((string)$selectedAccountId === (string)$acc->id)>{{ $acc->name }}</option>
                @endforeach
            </select>
            <button class="bg-slate-800 border border-slate-700 rounded p-2">Apply Account</button>
        </form>
        <div class="flex justify-end">
            <a href="{{ route('risk-ledger.export', request()->query()) }}" class="px-3 py-2 border border-slate-700 rounded-lg text-slate-200 text-sm">Export CSV</a>
        </div>

        @if($lockMode)
            <div class="bg-rose-500/20 border border-rose-500/50 rounded-2xl p-4 text-rose-200">
                <div class="font-semibold">AUTO LOCK WARNING ACTIVE</div>
                <div class="text-sm mt-1">Risk usage di atas 90%. Disarankan hentikan entry baru sampai risiko kembali aman.</div>
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-4">Daily P/L: <span class="{{ $daily>=0 ? 'text-emerald-400':'text-rose-400' }}">${{ number_format($daily,2) }}</span></div>
            <div class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-4">Weekly P/L: <span class="{{ $weekly>=0 ? 'text-emerald-400':'text-rose-400' }}">${{ number_format($weekly,2) }}</span></div>
            <div class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-4">Monthly P/L: <span class="{{ $monthly>=0 ? 'text-emerald-400':'text-rose-400' }}">${{ number_format($monthly,2) }}</span></div>
        </div>

        <div class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-slate-200 font-semibold">Prop Firm Risk Status</h3>
                <span class="px-2 py-1 rounded-full text-xs {{ $status==='safe' ? 'bg-emerald-500/20 text-emerald-300' : ($status==='warning' ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">{{ strtoupper($status) }}</span>
            </div>

            <div class="space-y-4 text-sm">
                <div>
                    <div class="flex justify-between text-slate-300 mb-1">
                        <span>Daily Drawdown Usage</span>
                        <span>{{ number_format($dailyLossUsed,2) }} / {{ number_format($dailyLimit,2) }} ({{ $dailyUsagePct }}%)</span>
                    </div>
                    <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full {{ $dailyUsagePct >= 100 ? 'bg-rose-500' : ($dailyUsagePct >= 75 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ $dailyUsagePct }}%"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Remaining Daily DD: <span class="text-amber-300">${{ number_format($remainingDaily,2) }}</span></p>
                </div>

                <div>
                    <div class="flex justify-between text-slate-300 mb-1">
                        <span>Max Drawdown Usage</span>
                        <span>{{ number_format($maxDrawdownUsed,2) }} / {{ number_format($maxLimit,2) }} ({{ $maxUsagePct }}%)</span>
                    </div>
                    <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full {{ $maxUsagePct >= 100 ? 'bg-rose-500' : ($maxUsagePct >= 75 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ $maxUsagePct }}%"></div>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Remaining Max DD: <span class="text-amber-300">${{ number_format($remainingMax,2) }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-4">
            <h3 class="text-slate-200 font-semibold mb-3">Riwayat Pelanggaran Risiko Harian (30 hari)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-slate-200">
                    <tr class="text-slate-400 border-b border-slate-800">
                        <th class="text-left py-2">Tanggal</th>
                        <th class="text-left">Day P/L</th>
                        <th class="text-left">Trades</th>
                        <th class="text-left">Usage</th>
                        <th class="text-left">Level</th>
                    </tr>
                    @forelse($dailyLogRows as $row)
                        <tr class="border-b border-slate-800/50">
                            <td class="py-2">{{ $row['date'] }}</td>
                            <td class="{{ $row['day_pl'] >=0 ? 'text-emerald-400' : 'text-rose-400' }}">${{ number_format($row['day_pl'],2) }}</td>
                            <td>{{ $row['trade_count'] }}</td>
                            <td>{{ number_format($row['usage'],2) }}%</td>
                            <td>
                                <span class="px-2 py-1 rounded-full text-xs {{ $row['level']==='breach' ? 'bg-rose-500/20 text-rose-300' : 'bg-amber-500/20 text-amber-300' }}">{{ strtoupper($row['level']) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-3 text-slate-500">Belum ada pelanggaran harian pada 30 hari terakhir.</td></tr>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

