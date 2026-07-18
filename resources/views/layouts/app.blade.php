<!DOCTYPE html>
<html class="overflow-x-hidden theme-{{ setting('theme', 'clean') }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @php
        $logoText = setting('logo_text', setting('site_name', 'Texas Lawn Legends'));
        $logoBadge = setting('logo_badge', 'EST. 2019 | Dallas, TX');
        $logoImage = setting_image('logo_image');
        $phone = setting('primary_phone', '(214) 617-7725');
        $email = setting('primary_email');
        $telHref = 'tel:+1' . preg_replace('/\D/', '', (string) $phone);
        $brandFont = setting('brand_font', 'Montserrat');
        $areas = (array) setting('service_areas', []);
        $current = request()->path();
    @endphp

    @include('partials.seo')
    @include('partials.analytics')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Brand font (non-default fonts load from Bunny Fonts CDN) --}}
    @if ($brandFont && $brandFont !== 'Montserrat')
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family={{ strtolower(str_replace(' ', '-', $brandFont)) }}:400,500,600,700,800,900&display=swap">
    @endif

    {{-- Brand tokens: remap the palette shades the design uses to CMS colors --}}
    <style>
        :root {
            @if ($brandFont && $brandFont !== 'Montserrat')
            --font-sans: '{{ $brandFont }}', ui-sans-serif, system-ui, sans-serif;
            @endif
            --color-emerald-900: {{ setting('color_primary', '#1b4332') }};
            --color-emerald-800: {{ setting('color_primary_light', '#2d6a4f') }};
            --color-yellow-400: {{ setting('color_accent', '#facc15') }};
            --color-slate-800: {{ setting('color_slate', '#334155') }};
        }
    </style>

    @livewireStyles

    @stack('head')
</head>
    <body class="min-h-screen bg-white font-sans text-slate-900 antialiased overflow-x-hidden">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:border-2 focus:border-slate-950 focus:bg-yellow-400 focus:px-4 focus:py-2 focus:text-sm focus:font-bold focus:uppercase focus:tracking-wide">Skip to content</a>


    {{-- ============================= HEADER ============================= --}}
    <header class="sticky top-0 z-50 w-full border-b-4 border-slate-950 bg-white"
            x-data="{ mobileOpen: false }"
            x-init="$watch('mobileOpen', v => document.body.classList.toggle('overflow-hidden', v));
                   const setH = () => document.documentElement.style.setProperty('--header-h', $el.offsetHeight + 'px');
                   setH();
                   window.addEventListener('resize', setH);"
            @keydown.escape.window="mobileOpen = false">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-3">
            <a href="{{ url('/') }}" class="flex flex-col leading-none">
                @if ($logoImage)
                    <img src="{{ $logoImage }}" alt="{{ $logoText }}" class="h-10 w-auto max-w-[220px] object-contain">
                @else
                    <span class="text-lg font-black uppercase tracking-tight text-slate-900 sm:text-xl">{{ $logoText }}</span>
                    @if ($logoBadge)
                        <span class="mt-1 inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">{{ $logoBadge }}</span>
                    @endif
                @endif
            </a>

            @php
                $navItems = [
                    'Our Services' => url('/services'),
                    'Portfolio' => url('/portfolio'),
                    'Client Portal' => url('/portal'),
                    'About Us' => url('/about'),
                ];
            @endphp

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ($navItems as $label => $href)
                    <a href="{{ $href }}"
                       @class([
                           'px-2 py-1 text-sm font-bold uppercase tracking-wide transition-colors',
                           'bg-yellow-400' => $current === ltrim($href, '/'),
                           'text-slate-900 hover:bg-yellow-400' => $current !== ltrim($href, '/'),
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ $telHref }}" class="hidden border-2 border-slate-950 px-2 py-1.5 text-[10px] sm:inline-block sm:px-3 sm:py-2 sm:text-xs font-medium uppercase tracking-wide text-slate-900 transition-colors hover:bg-slate-100">
                    {{ $phone }}
                </a>

                {{-- Mobile hamburger --}}
                <button type="button"
                        class="inline-flex h-11 w-11 items-center justify-center border-2 border-slate-950 bg-yellow-400 text-slate-950 transition-colors hover:bg-yellow-300 lg:hidden"
                        @click="mobileOpen = !mobileOpen"
                        :class="mobileOpen ? 'bg-slate-950 text-yellow-400' : ''"
                        :aria-expanded="mobileOpen"
                        aria-label="Toggle navigation menu">
                    <svg x-show="!mobileOpen" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
                    <svg x-show="mobileOpen" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile backdrop --}}
        <div x-show="mobileOpen" x-cloak
             x-transition.opacity
             @click="mobileOpen = false"
             class="fixed inset-x-0 bottom-0 z-40 bg-slate-950/40 lg:hidden"
             style="top: var(--header-h, 57px);"></div>

        {{-- Mobile menu panel --}}
        <nav x-show="mobileOpen" x-cloak
             x-transition
             class="fixed inset-x-0 z-50 flex flex-col overflow-y-auto border-t-2 border-slate-950 bg-white shadow-xl lg:hidden"
             style="top: var(--header-h, 57px); height: calc(100dvh - var(--header-h, 57px));">
            <div class="mx-auto flex w-full flex-col px-6 py-2">
                @foreach ($navItems as $label => $href)
                    <a href="{{ $href }}" @click="mobileOpen = false"
                       @class([
                           'border-b border-slate-200 py-3 text-sm font-bold uppercase tracking-wide transition-colors',
                           'bg-yellow-400' => $current === ltrim($href, '/'),
                           'text-slate-900 hover:bg-yellow-400' => $current !== ltrim($href, '/'),
                       ])>{{ $label }}</a>
                @endforeach
                <a href="{{ $telHref }}" @click="mobileOpen = false" class="flex items-center gap-2 py-3 text-sm font-bold uppercase tracking-wide text-slate-900 transition-colors hover:bg-yellow-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                    {{ $phone }}
                </a>
            </div>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    {{-- ============================= FOOTER ============================= --}}
    <footer class="border-t-4 border-slate-950 bg-slate-950 px-6 py-12 text-slate-200">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <span class="text-lg font-black uppercase tracking-tight text-white">{{ $logoText }}</span>
                @if ($logoBadge)
                    <span class="mt-2 inline-block w-fit bg-emerald-900 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-yellow-400">{{ $logoBadge }}</span>
                @endif
                <div class="mt-5 space-y-1.5 text-sm">
                    <p><a href="{{ $telHref }}" class="font-bold text-white hover:text-yellow-400">{{ $phone }}</a></p>
                    @if ($email)<p><a href="mailto:{{ $email }}" class="hover:text-yellow-400">{{ $email }}</a></p>@endif
                    @if ($note = setting('footer_note'))<p class="text-slate-400">{{ $note }}</p>@endif
                </div>
                @if ($copyright = setting('footer_copyright'))
                    <p class="mt-6 text-xs leading-relaxed text-slate-500">{{ $copyright }}</p>
                @endif
            </div>

            <div>
                <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-yellow-400">Design &amp; Create</h3>
                <ul class="space-y-2 text-sm">
                    @foreach (\App\Models\Service::query()->active()->createSuite()->ordered()->pluck('title') as $item)
                        <li><a href="{{ url('/#create-suite') }}" class="text-slate-300 transition-colors hover:text-white">{{ $item }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-yellow-400">Property Maintenance</h3>
                <ul class="space-y-2 text-sm">
                    @foreach (\App\Models\Service::query()->active()->careSuite()->ordered()->pluck('title') as $item)
                        <li><a href="{{ url('/#care-suite') }}" class="text-slate-300 transition-colors hover:text-white">{{ $item }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-yellow-400">Core Hub</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url('/portal') }}" class="text-slate-300 transition-colors hover:text-white">Client Portal Login</a></li>
                    <li><a href="{{ url('/privacy') }}" class="text-slate-300 transition-colors hover:text-white">Privacy Compliance Terms</a></li>
                    <li><a href="{{ url('/admin') }}" class="text-slate-300 transition-colors hover:text-white">Administrative CMS Control Panel</a></li>
                </ul>
            </div>
        </div>
    </footer>

    {{-- Sticky Get-Estimate call to action --}}
    <a href="{{ url('/estimate') }}"
       class="btn-brutal fixed bottom-4 right-4 z-40 border-2 border-slate-950 bg-yellow-400 px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-950 shadow-lg sm:bottom-6 sm:right-6 sm:px-5 sm:py-3 sm:text-sm">
        Get Estimate
    </a>

    @livewireScripts
</body>
</html>
