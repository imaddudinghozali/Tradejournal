<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-cyan-400 border border-cyan-300 rounded-lg font-semibold text-xs text-slate-950 uppercase tracking-widest hover:bg-cyan-300 focus:bg-cyan-300 active:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:ring-offset-2 focus:ring-offset-slate-900 transition ease-in-out duration-150 shadow-[0_0_18px_rgba(34,211,238,.28)]']) }}>
    {{ $slot }}
</button>
