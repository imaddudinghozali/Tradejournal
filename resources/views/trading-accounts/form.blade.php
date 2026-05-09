<x-app-layout>
<x-slot name="header"><h2 class="text-cyan-300 text-3xl font-semibold tracking-tight">Account Form</h2></x-slot>
<form method="POST" action="{{ $account->exists ? route('trading-accounts.update',$account) : route('trading-accounts.store') }}" class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-6 text-slate-200 space-y-4 shadow-xl shadow-cyan-950/20">
@csrf @if($account->exists) @method('PUT') @endif
<div><label class="text-sm text-slate-400">Nama Account</label><input name="name" value="{{ old('name',$account->name) }}" placeholder="Contoh: XAU Scalper 10K" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
<div><label class="text-sm text-slate-400">Broker</label><input name="broker" value="{{ old('broker',$account->broker) }}" placeholder="Contoh: FTMO / IC Markets" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"><p class="text-xs text-slate-500 mt-1">Opsional, isi nama broker atau prop firm.</p></div>
<div><label class="text-sm text-slate-400">Nomor Account</label><input name="account_number" value="{{ old('account_number',$account->account_number) }}" placeholder="Contoh: HRL-001" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"></div>
<div class="grid md:grid-cols-2 gap-3"><div><label class="text-sm text-slate-400">Balance (USD)</label><input name="balance" type="number" step="0.01" value="{{ old('balance',$account->balance) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required><p class="text-xs text-slate-500 mt-1">Saldo akun saat ini.</p></div><div><label class="text-sm text-slate-400">Equity (USD)</label><input name="equity" type="number" step="0.01" value="{{ old('equity',$account->equity) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required><p class="text-xs text-slate-500 mt-1">Balance + floating P/L.</p></div></div>
@if($errors->any())<div class="text-rose-400 text-sm">{{ $errors->first() }}</div>@endif
<button class="bg-cyan-400 px-4 py-2 rounded-lg text-slate-950 shadow-[0_0_18px_rgba(34,211,238,.35)]">Save Account</button>
</form>
</x-app-layout>
