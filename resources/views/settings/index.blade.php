<x-app-layout>
<x-slot name="header"><h2 class="text-cyan-300 text-3xl font-semibold tracking-tight">Risk Settings</h2></x-slot>
<form method="POST" action="{{ route('settings.update') }}" class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-6 shadow-xl shadow-cyan-950/20 text-slate-200 space-y-4">
@csrf
<div><label class="block text-sm text-slate-400">Risk Per Trade (%)</label><input name="risk_per_trade_percent" type="number" step="0.01" value="{{ old('risk_per_trade_percent',$setting->risk_per_trade_percent ?? 1) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required><p class="text-xs text-slate-500 mt-1">Contoh: 1 berarti risiko 1% per posisi.</p></div>
<div><label class="block text-sm text-slate-400">Daily Drawdown Limit (USD)</label><input name="daily_drawdown_limit" type="number" step="0.01" value="{{ old('daily_drawdown_limit',$setting->daily_drawdown_limit ?? 0) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
<div><label class="block text-sm text-slate-400">Max Drawdown Limit (USD)</label><input name="max_drawdown_limit" type="number" step="0.01" value="{{ old('max_drawdown_limit',$setting->max_drawdown_limit ?? 0) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
<div><label class="block text-sm text-slate-400">Warning Threshold (%)</label><input name="warning_threshold_percent" type="number" step="0.01" value="{{ old('warning_threshold_percent',$setting->warning_threshold_percent ?? 75) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required><p class="text-xs text-slate-500 mt-1">Contoh: 75 berarti muncul warning saat limit terpakai 75%.</p></div>

<div>
    <label class="block text-sm text-slate-400">Intensitas Efek UI</label>
    <select id="neonMode" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5">
        <option value="soft">Soft</option>
        <option value="medium">Medium</option>
        <option value="strong">Strong</option>
    </select>
    <p class="text-xs text-slate-500 mt-1">Hanya mengubah tampilan, tidak memengaruhi data trading.</p>
</div>

@if($errors->any())<div class="text-rose-400 text-sm">{{ $errors->first() }}</div>@endif
<button class="bg-cyan-400 text-slate-950 px-4 py-2 rounded-lg shadow-[0_0_18px_rgba(34,211,238,.35)]">Save Settings</button>
</form>

<script>
(() => {
    const select = document.getElementById('neonMode');
    if (!select) return;
    const stored = localStorage.getItem('hrl_neon') || 'medium';
    select.value = stored;
    select.addEventListener('change', () => {
        localStorage.setItem('hrl_neon', select.value);
        const body = document.body;
        body.setAttribute('data-neon', select.value);
        if (window.Alpine && Alpine.store) {
            // no-op for compatibility
        }
    });
})();
</script>
</x-app-layout>
