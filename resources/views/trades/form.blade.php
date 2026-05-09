<x-app-layout>
    <x-slot name="header"><h2 class="text-cyan-300 text-3xl font-semibold tracking-tight">Trade Form</h2></x-slot>
    <form method="POST" enctype="multipart/form-data" action="{{ $trade->exists ? route('trades.update',$trade) : route('trades.store') }}" class="bg-slate-900/90 border border-cyan-900/40 rounded-2xl p-6 shadow-xl shadow-cyan-950/20 text-slate-200 space-y-4">
        @csrf @if($trade->exists) @method('PUT') @endif

        <div>
            <label class="text-sm text-slate-400">Trading Account</label>
            <select name="trading_account_id" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5">
                @foreach($accounts as $a)<option value="{{ $a->id }}" @selected(old('trading_account_id',$trade->trading_account_id)==$a->id)>{{ $a->name }}</option>@endforeach
            </select>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div><label class="text-sm text-slate-400">Tanggal Trade</label><input type="date" name="trade_date" value="{{ old('trade_date',optional($trade->trade_date)->format('Y-m-d')) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
            <div><label class="text-sm text-slate-400">Pair</label><input name="pair" placeholder="Contoh: XAU/USD" value="{{ old('pair',$trade->pair ?? 'XAU/USD') }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required><p class="text-xs text-slate-500 mt-1">Gunakan format pasangan instrumen yang konsisten.</p></div>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div><label class="text-sm text-slate-400">Session</label><input name="session" placeholder="Contoh: London / New York" value="{{ old('session',$trade->session) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"></div>
            <div><label class="text-sm text-slate-400">Setup Type</label><input name="setup_type" placeholder="Contoh: Breakout Retest" value="{{ old('setup_type',$trade->setup_type) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"></div>
        </div>

        <div class="grid md:grid-cols-3 gap-3">
            <div><label class="text-sm text-slate-400">Entry Price</label><input name="entry_price" type="number" step="0.01" value="{{ old('entry_price',$trade->entry_price) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"></div>
            <div><label class="text-sm text-slate-400">Stop Loss</label><input name="stop_loss" type="number" step="0.01" value="{{ old('stop_loss',$trade->stop_loss) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"><p class="text-xs text-slate-500 mt-1">Wajib jika Entry diisi.</p></div>
            <div><label class="text-sm text-slate-400">Take Profit</label><input name="take_profit" type="number" step="0.01" value="{{ old('take_profit',$trade->take_profit) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"></div>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <div><label class="text-sm text-slate-400">Lot Size</label><input name="lot_size" type="number" step="0.01" placeholder="Contoh: 0.10" value="{{ old('lot_size',$trade->lot_size ?? 0.01) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
            <div><label class="text-sm text-slate-400">Risk Amount (USD)</label><input name="risk_amount" type="number" step="0.01" placeholder="Contoh: 50" value="{{ old('risk_amount',$trade->risk_amount ?? 0) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
            <div><label class="text-sm text-slate-400">Risk Percent (%)</label><input name="risk_percent" type="number" step="0.01" placeholder="Contoh: 1.00" value="{{ old('risk_percent',$trade->risk_percent ?? 0) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
            <div><label class="text-sm text-slate-400">Reward Amount (USD)</label><input name="reward_amount" type="number" step="0.01" placeholder="Contoh: 120" value="{{ old('reward_amount',$trade->reward_amount ?? 0) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
            <div><label class="text-sm text-slate-400">R:R Ratio</label><input name="rr_ratio" type="number" step="0.01" placeholder="Contoh: 1.50" value="{{ old('rr_ratio',$trade->rr_ratio ?? 0) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
            <div><label class="text-sm text-slate-400">Profit / Loss (USD)</label><input name="profit_loss" type="number" step="0.01" placeholder="Profit isi positif, loss isi negatif" value="{{ old('profit_loss',$trade->profit_loss ?? 0) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" required></div>
        </div>

        <div><label class="text-sm text-slate-400">AMDX Phase</label><input name="amdx_phase" placeholder="Contoh: Accumulation / Manipulation / Distribution / Expansion" value="{{ old('amdx_phase',$trade->amdx_phase) }}" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5"></div>

        <div class="grid md:grid-cols-3 gap-2 text-sm">
            @php
                $flags = [
                    'liquidity_sweep' => 'Liquidity Sweep',
                    'inducement' => 'Inducement',
                    'fvg' => 'FVG',
                    'order_block' => 'Order Block',
                    'bos' => 'BOS',
                    'choch' => 'CHOCH',
                ];
            @endphp
            @foreach($flags as $key => $label)
                <label class="inline-flex items-center gap-2 bg-slate-950 border border-slate-700 rounded-lg p-2.5">
                    <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $trade->{$key}))>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <div><label class="text-sm text-slate-400">Catatan Trade</label><textarea name="notes" rows="3" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" placeholder="Alasan entry, manajemen posisi, dan evaluasi setup">{{ old('notes',$trade->notes) }}</textarea></div>
        <div><label class="text-sm text-slate-400">Catatan Psikologi</label><textarea name="psychology_note" rows="3" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5" placeholder="Kondisi emosi, disiplin, fear/greed, confidence">{{ old('psychology_note',$trade->psychology_note) }}</textarea></div>

        <div>
            <label class="text-sm text-slate-400">Screenshot Trade (opsional)</label>
            <input type="file" name="screenshot" accept="image/*" class="w-full mt-1 bg-slate-950 border border-slate-700 rounded-lg p-2.5">
            <p class="text-xs text-slate-500 mt-1">Format gambar, maksimal 4MB.</p>
        </div>

        @if($trade->screenshot_path)<a target="_blank" href="{{ asset('storage/'.$trade->screenshot_path) }}" class="text-yellow-400 text-sm">Lihat screenshot saat ini</a>@endif
        @if($errors->any())<div class="text-rose-400 text-sm">{{ $errors->first() }}</div>@endif
        <button class="bg-cyan-400 text-slate-950 px-4 py-2 rounded-lg shadow-[0_0_18px_rgba(34,211,238,.35)]">Save Trade</button>
    </form>
</x-app-layout>

