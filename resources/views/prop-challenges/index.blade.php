<x-app-layout>
    <x-slot name="header"><h2 class="text-fuchsia-300 text-3xl font-semibold tracking-tight">Prop Challenges</h2></x-slot>

    <div class="bg-slate-900/90 border border-fuchsia-900/40 rounded-2xl p-5 shadow-xl shadow-fuchsia-950/20">
        <a href="{{ route('prop-challenges.create') }}" class="px-3 py-2 bg-fuchsia-400 text-slate-950 rounded-lg shadow-[0_0_18px_rgba(232,121,249,.35)]">+ Add Challenge</a>

        <table class="w-full mt-4 text-slate-200 text-sm">
            <tr class="border-b border-slate-800 text-slate-400"><th class="text-left py-2">Firm</th><th class="text-left">Type</th><th class="text-left">Status</th><th class="text-left">Progress</th><th class="text-left">Actions</th></tr>
            @forelse($challenges as $c)
                <tr class="border-b border-slate-800/40">
                    <td class="py-2 text-slate-100">{{ $c->prop_firm_name }}</td><td>{{ $c->account_type }}</td><td>{{ $c->challenge_status }}</td><td class="text-fuchsia-300">{{ $c->progress_percentage }}%</td>
                    <td class="py-2"><div class="flex items-center gap-3"><a class="text-fuchsia-300" href="{{ route('prop-challenges.edit',$c) }}">Edit</a><form method="POST" action="{{ route('prop-challenges.destroy',$c) }}" onsubmit="return confirm('Hapus challenge ini?');">@csrf @method('DELETE')<button type="submit" class="text-rose-400">Delete</button></form></div></td>
                </tr>
            @empty<tr><td colspan="5" class="py-5 text-slate-500">Belum ada prop challenge aktif.</td></tr>@endforelse
        </table>
    </div>
</x-app-layout>

