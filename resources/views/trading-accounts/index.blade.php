<x-app-layout>
    <x-slot name="header"><h2 class="text-cyan-300 text-3xl font-semibold tracking-tight">Trading Accounts</h2></x-slot>

    <div class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-5 shadow-xl shadow-cyan-950/20">
        <a href="{{ route('trading-accounts.create') }}" class="px-3 py-2 bg-cyan-400 text-slate-950 rounded-lg shadow-[0_0_18px_rgba(34,211,238,.35)]">+ Add Account</a>

        <table class="w-full mt-4 text-slate-200 text-sm">
            <tr class="border-b border-slate-800 text-slate-400">
                <th class="text-left py-2">Name</th><th class="text-left">Broker</th><th class="text-left">Balance</th><th class="text-left">Actions</th>
            </tr>
            @forelse($accounts as $a)
                <tr class="border-b border-slate-800/40">
                    <td class="py-2 text-slate-100">{{ $a->name }}</td>
                    <td>{{ $a->broker }}</td>
                    <td class="text-cyan-300">{{ number_format($a->balance,2) }}</td>
                    <td class="py-2"><div class="flex items-center gap-3"><a class="text-cyan-300" href="{{ route('trading-accounts.edit',$a) }}">Edit</a><form method="POST" action="{{ route('trading-accounts.destroy',$a) }}" onsubmit="return confirm('Hapus account ini?');">@csrf @method('DELETE')<button type="submit" class="text-rose-400">Delete</button></form></div></td>
                </tr>
            @empty<tr><td colspan="4" class="py-5 text-slate-500">Belum ada trading account. Tambahkan account pertama kamu.</td></tr>@endforelse
        </table>
    </div>
</x-app-layout>

