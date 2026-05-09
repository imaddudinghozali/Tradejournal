<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-2xl text-cyan-300">Trade Journal</h2></x-slot>
    <div class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-5 shadow-xl shadow-cyan-950/20">
        <form method="GET" class="space-y-2 mb-4">
            <div class="flex flex-wrap gap-2 text-xs">
                @foreach(['today' => 'Today', 'weekly' => 'WTD', 'month' => 'MTD', '3months' => '3M', '1year' => '1Y', 'custom' => 'Custom'] as $key => $label)
                    <button name="period" value="{{ $key }}" class="px-3 py-1 rounded-full border {{ $period === $key ? 'bg-yellow-500 text-slate-950 border-yellow-500' : 'bg-slate-900 text-slate-300 border-slate-700' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="grid md:grid-cols-5 gap-2">
                <select name="account_id" class="bg-slate-950 border border-slate-700 rounded p-2 md:col-span-2">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)<option value="{{ $acc->id }}" @selected((string)$selectedAccountId === (string)$acc->id)>{{ $acc->name }}</option>@endforeach
                </select>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-slate-950 border border-slate-700 rounded p-2">
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-slate-950 border border-slate-700 rounded p-2">
                <button class="bg-slate-800 border border-slate-700 rounded p-2">Apply</button>
            </div>
        </form>

        <div class="grid sm:grid-cols-3 gap-3 mb-4 text-sm">
            <div class="bg-slate-950 border border-slate-800 rounded p-3">Total P/L: <span class="{{ $summaryTotalPL >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">{{ number_format($summaryTotalPL,2) }}</span></div>
            <div class="bg-slate-950 border border-slate-800 rounded p-3">Wins: <span class="text-emerald-400">{{ $summaryWins }}</span></div>
            <div class="bg-slate-950 border border-slate-800 rounded p-3">Losses: <span class="text-rose-400">{{ $summaryLosses }}</span></div>
        </div>

        <div class="mb-4 flex flex-wrap gap-2">
            <a href="{{ route('trades.create') }}" class="px-3 py-2 bg-cyan-400 text-slate-950 rounded-lg shadow-[0_0_18px_rgba(34,211,238,.35)]">+ Add Trade</a>
            <a href="{{ route('trades.export', request()->query()) }}" class="px-3 py-2 border border-slate-700 rounded-lg text-slate-200">Export CSV</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-200">
                <tr class="text-slate-400 border-b border-slate-800"><th class="text-left py-2">Date</th><th class="text-left">Pair</th><th class="text-left">Result</th><th class="text-left">P/L</th><th class="text-left">AMDX</th><th class="text-left">Screenshot</th><th class="text-left">Actions</th></tr>
                @foreach($trades as $t)
                    <tr class="border-b border-slate-800/50"><td class="py-2">{{ $t->trade_date->format('Y-m-d') }}</td><td>{{ $t->pair }}</td><td class="uppercase">{{ $t->result }}</td><td class="{{ $t->profit_loss >=0 ? 'text-emerald-400':'text-rose-400' }}">{{ number_format($t->profit_loss,2) }}</td><td>{{ $t->amdx_phase }}</td><td>@if($t->screenshot_path)<a class="text-cyan-300" target="_blank" href="{{ asset('storage/'.$t->screenshot_path) }}">View</a>@else - @endif</td><td class="py-2"><div class="flex items-center gap-3"><a href="{{ route('trades.edit',$t) }}" class="text-cyan-300">Edit</a><form method="POST" action="{{ route('trades.destroy',$t) }}" onsubmit="return confirm('Hapus trade ini?');">@csrf @method('DELETE') <button type="submit" class="text-rose-400">Delete</button></form></div></td></tr>
                @endforeach
            </table>
        </div>
        <div class="mt-4">{{ $trades->links() }}</div>
    </div>
</x-app-layout>

