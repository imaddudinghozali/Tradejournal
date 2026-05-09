<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HardRiskLedger') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root { --neo-alpha: .28; }
        [data-neon='soft'] { --neo-alpha: .18; }
        [data-neon='medium'] { --neo-alpha: .28; }
        [data-neon='strong'] { --neo-alpha: .42; }
        [x-cloak] { display: none !important; }

        .tap-anim {
            position: relative;
            overflow: hidden;
            transform: translateZ(0);
            transition: transform .22s cubic-bezier(.22,.61,.36,1), box-shadow .25s cubic-bezier(.22,.61,.36,1), filter .2s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .tap-anim:hover {
            filter: brightness(1.02);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .24), 0 0 0 1px rgba(148, 163, 184, .16) inset;
        }
        .tap-anim:active { transform: scale(.962) translateY(1px); }

        .tap-flash {
            box-shadow: 0 0 0 1px rgba(148,163,184,.32), 0 0 12px rgba(15,23,42,.22) !important;
            transition: box-shadow .1s ease;
        }

        .ripple {
            position: absolute;
            border-radius: 9999px;
            transform: scale(0);
            animation: ripple .62s cubic-bezier(.2,.8,.2,1);
            background: radial-gradient(circle, rgba(148,163,184,.34) 0%, rgba(100,116,139,.18) 46%, rgba(148,163,184,0) 78%);
            pointer-events: none;
            mix-blend-mode: normal;
            opacity: .72;
        }

        @keyframes ripple {
            0% { transform: scale(0); opacity: .72; }
            70% { opacity: .34; }
            100% { transform: scale(5); opacity: 0; }
        }

        @media (prefers-reduced-motion: reduce) {
            .tap-anim, .ripple { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('hrl_sidebar') === 'collapsed',
        neonMode: localStorage.getItem('hrl_neon') || 'medium',
        toggleSidebar() { this.sidebarCollapsed = !this.sidebarCollapsed; localStorage.setItem('hrl_sidebar', this.sidebarCollapsed ? 'collapsed' : 'expanded'); }
    }"
    x-bind:data-neon="neonMode"
    class="font-sans antialiased bg-slate-950 text-slate-100"
>
<div class="min-h-screen flex">
    @include('layouts.navigation')

    <div class="flex-1 min-w-0">
        @isset($header)
            <header class="bg-slate-900/80 border-b border-slate-800">
                <div class="px-4 md:px-6 py-4 md:py-5 flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg border border-slate-700 text-slate-300" aria-label="Open menu" title="Open menu"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg></button>
                    <button @click="toggleSidebar" class="hidden md:inline-flex p-2 rounded-lg border border-slate-700 text-slate-300" aria-label="Toggle sidebar" title="Toggle sidebar"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"/></svg></button>
                    <div class="min-w-0 flex-1">{{ $header }}</div>
                </div>
            </header>
        @endisset
        <main class="p-4 md:p-6">{{ $slot }}</main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const selector = 'button, a, [role="button"], .rounded-xl, .rounded-2xl';
    document.querySelectorAll(selector).forEach(el => el.classList.add('tap-anim'));

    const spawnRipple = (el, x, y) => {
        const rect = el.getBoundingClientRect();
        const ripple = document.createElement('span');
        const size = Math.max(rect.width, rect.height) * 1.15;
        ripple.className = 'ripple';
        ripple.style.width = ripple.style.height = `${size}px`;
        ripple.style.left = `${x - rect.left - size / 2}px`;
        ripple.style.top = `${y - rect.top - size / 2}px`;
        el.appendChild(ripple);
        setTimeout(() => ripple.remove(), 860);
    };

    document.addEventListener('click', (e) => {
        const el = e.target.closest('.tap-anim');
        if (!el) return;
        el.classList.add('tap-flash');
        setTimeout(() => el.classList.remove('tap-flash'), 140);
        spawnRipple(el, e.clientX, e.clientY);
    });

    document.addEventListener('touchstart', (e) => {
        const t = e.target.closest('.tap-anim');
        if (!t) return;
        const touch = e.touches[0];
        t.classList.add('tap-flash');
        setTimeout(() => t.classList.remove('tap-flash'), 180);
        if (touch) spawnRipple(t, touch.clientX, touch.clientY);
    }, { passive: true });
});
</script>
</body>
</html>
