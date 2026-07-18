<!DOCTYPE html>
<html class="overflow-x-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

    {{-- ============================= HEADER ============================= --}}
    <header class="sticky top-0 z-50 w-full border-b-4 border-slate-950 bg-white">
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

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ([
                    'Our Services' => url('/#services'),
                    'Portfolio' => url('/#portfolio'),
                    'Client Portal' => url('/portal'),
                    'About Us' => url('/#about'),
                ] as $label => $href)
                    <a href="{{ $href }}" class="px-2 py-1 text-sm font-bold uppercase tracking-wide text-slate-900 transition-colors hover:bg-yellow-400">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ $telHref }}" class="border-2 border-slate-950 px-2 py-1.5 text-[10px] sm:px-3 sm:py-2 sm:text-xs font-medium uppercase tracking-wide text-slate-900 transition-colors hover:bg-slate-100 inline-block">
                    {{ $phone }}
                </a>
            </div>
        </div>
    </header>

    <main>
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

    @livewireScripts
</body>
</html>
