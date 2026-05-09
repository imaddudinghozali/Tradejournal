@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-slate-950 border border-slate-700 text-slate-100 focus:border-cyan-400 focus:ring-cyan-400 rounded-lg shadow-sm placeholder:text-slate-500']) }}>
