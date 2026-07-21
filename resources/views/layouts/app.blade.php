<!DOCTYPE html>
<html class="overflow-x-hidden theme-{{ request('preview_theme', setting('theme', 'clean')) }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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


    {{-- Global Modal Container Wrapper --}}
    <div x-data="{ estimateModalOpen: false, modalLocation: '' }"
         @open-estimate-modal.window="estimateModalOpen = true; if ($event.detail && $event.detail.location) modalLocation = $event.detail.location;"
         @keydown.escape.window="estimateModalOpen = false">

        {{-- Combined Sticky Header Region --}}
        <header class="sticky top-0 z-50 w-full"
                x-data="{ mobileOpen: false }"
                x-init="$watch('mobileOpen', v => document.body.classList.toggle('overflow-hidden', v));
                       const setH = () => document.documentElement.style.setProperty('--header-h', $el.offsetHeight + 'px');
                       setH();
                       window.addEventListener('resize', setH);"
                @keydown.escape.window="mobileOpen = false">

            {{-- Top Info & Phone Banner --}}
            <div class="border-b-2 border-slate-950 bg-slate-950 px-6 py-2 text-xs text-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <div class="flex items-center gap-2 font-bold uppercase tracking-widest text-yellow-400">
                        <span>📍 Dallas, TX</span>
                        <span class="hidden text-slate-600 sm:inline">·</span>
                        <span class="hidden text-slate-300 sm:inline">Premier Landscape Design & Hardscaping</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ $telHref }}" class="flex items-center gap-1.5 font-bold uppercase tracking-wider text-white transition-colors hover:text-yellow-400">
                            <svg class="h-3.5 w-3.5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            <span>Call or Text: {{ $phone }}</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Main Navigation Bar --}}
            <div class="w-full border-b-4 border-slate-950 bg-white">
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
                    <a href="{{ url('/estimate') }}" @click.prevent="$dispatch('open-estimate-modal')" class="btn-brutal bg-yellow-400 px-3.5 py-2 text-xs font-black uppercase tracking-wider text-slate-950 sm:px-5">
                        Get Free Estimate
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

            {{-- Mobile nav panel --}}
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
                        @foreach (\App\Support\PageBlockData::createServices()->pluck('title') as $item)
                            <li><a href="{{ url('/#create-suite') }}" class="text-slate-300 transition-colors hover:text-white">{{ $item }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-yellow-400">Property Maintenance</h3>
                    <ul class="space-y-2 text-sm">
                        @foreach (\App\Support\PageBlockData::careServices()->pluck('title') as $item)
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

        {{-- Themeable Mobile Sticky Action Rail --}}
        <div class="fixed bottom-0 inset-x-0 z-40 border-t-4 border-slate-950 bg-brand-paper p-3 shadow-2xl lg:hidden">
            <div class="mx-auto flex max-w-md items-center justify-between gap-3">
                <a href="{{ url('/estimate') }}" @click.prevent="$dispatch('open-estimate-modal')" class="btn-brutal flex-1 bg-yellow-400 py-3 text-center text-xs font-black uppercase tracking-wider text-slate-950">
                    ⚡ 2-Min Price Quote
                </a>
                <a href="{{ $telHref }}" class="flex items-center justify-center border-2 border-slate-950 bg-white px-4 py-3 text-xs font-black uppercase tracking-wider text-slate-900 transition-colors hover:bg-slate-100">
                    📞 Call
                </a>
            </div>
        </div>

        {{-- Sticky Get-Estimate call to action (Desktop) --}}
        <a href="{{ url('/estimate') }}"
           @click.prevent="$dispatch('open-estimate-modal')"
           class="btn-brutal fixed bottom-6 right-6 z-30 hidden border-2 border-slate-950 bg-yellow-400 px-5 py-3 text-sm font-bold uppercase tracking-wide text-slate-950 shadow-lg lg:block">
            Get Estimate
        </a>

        {{-- Instant Property Valuation Popup Modal --}}
        <div x-show="estimateModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm sm:p-6"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="estimateModalOpen = false">
            
            <div class="box-brutal w-full max-w-xl overflow-hidden bg-white p-6 sm:p-8" @click.stop
                 x-data="{
                     sqft: 1200,
                     serviceScope: 'design_build',
                     low() { return Math.round(this.sqft * 0.85); },
                     high() { return Math.round(this.sqft * 1.45); }
                 }">
                <div class="flex items-start justify-between gap-4 border-b-2 border-slate-950 pb-4">
                    <div>
                        <span class="inline-block bg-yellow-400 px-2.5 py-1 text-[10px] font-black uppercase tracking-widest text-slate-950">⚡ Instant Valuation</span>
                        <h3 class="mt-2 text-2xl font-black uppercase tracking-tight text-slate-900 sm:text-3xl">Property Estimate Preview</h3>
                    </div>
                    <button type="button" @click="estimateModalOpen = false" class="border-2 border-slate-950 bg-white px-2.5 py-1 text-xs font-black uppercase tracking-widest text-slate-900 hover:bg-yellow-400">✕ Close (Esc)</button>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-700">1. Property Location / Neighborhood</label>
                        <input type="text" x-model="modalLocation" placeholder="Enter Dallas ZIP code or neighborhood..." class="mt-1.5 w-full border-2 border-slate-950 px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-700">2. Primary Service Scope</label>
                        <div class="mt-2 grid grid-cols-2 gap-2 text-xs font-bold">
                            <button type="button" @click="serviceScope = 'design_build'"
                                    :class="serviceScope === 'design_build' ? 'bg-slate-950 text-yellow-400 border-slate-950' : 'bg-white text-slate-900 border-slate-950 hover:bg-slate-50'"
                                    class="border-2 p-2.5 text-left uppercase tracking-tight transition-colors">
                                Hardscaping & Design
                            </button>
                            <button type="button" @click="serviceScope = 'maintenance'"
                                    :class="serviceScope === 'maintenance' ? 'bg-slate-950 text-yellow-400 border-slate-950' : 'bg-white text-slate-900 border-slate-950 hover:bg-slate-50'"
                                    class="border-2 p-2.5 text-left uppercase tracking-tight transition-colors">
                                Lawn Care & Maintain
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-black uppercase tracking-widest text-slate-700">
                            <span>3. Approx. Area Size</span>
                            <span class="font-mono text-slate-900"><span x-text="Number(sqft).toLocaleString()"></span> sq ft</span>
                        </div>
                        <input type="range" min="300" max="5000" step="100" x-model.number="sqft" class="mt-2 h-3 w-full cursor-pointer appearance-none border-2 border-slate-950 bg-slate-100 accent-yellow-400">
                    </div>

                    <div class="box-brutal bg-slate-950 p-4 text-center text-white">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Real-Time Valuation Preview</span>
                        <p class="mt-1 text-3xl font-black text-yellow-400">
                            $<span x-text="low().toLocaleString()"></span> <span class="text-slate-400">–</span> $<span x-text="high().toLocaleString()"></span>
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a :href="'{{ url('/estimate') }}?neighborhood=' + encodeURIComponent(modalLocation) + '&sqft=' + sqft"
                       class="btn-brutal flex-1 bg-yellow-400 px-6 py-3.5 text-center text-xs font-black uppercase tracking-wider text-slate-950">
                        Lock In Free Site Visit →
                    </a>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    @auth
        <div x-data="{
            version: '{{ \Illuminate\Support\Facades\Cache::get('site_version', 1) }}',
            init() {
                setInterval(() => {
                    fetch('/api/site-version')
                        .then(res => res.json())
                        .then(data => {
                            if (data.version != this.version) {
                                window.location.reload();
                            }
                        });
                }, 2000);
            }
        }"></div>
    @endauth
</body>
</html>
