<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Demo hub — pick an industry</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 font-sans text-white antialiased">
    <div class="mx-auto max-w-5xl px-4 py-16 md:px-8">
        <p class="text-xs font-black uppercase tracking-widest text-yellow-400">Sales demo hub</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight sm:text-5xl">Pick an industry model home</h1>
        <p class="mt-4 max-w-2xl text-base text-slate-300">
            One click loads that starter kit on this install. Walk the hero, quote funnel, and admin — then reset from admin when the meeting ends.
        </p>

        <div class="mt-12 grid gap-6 sm:grid-cols-3">
            @foreach ($cards as $card)
                <form method="POST" action="{{ route('demo.load') }}" class="flex flex-col border-2 border-white/20 bg-slate-900 p-6 transition hover:border-yellow-400">
                    @csrf
                    <input type="hidden" name="niche" value="{{ $card['id'] }}">
                    <h2 class="text-xl font-black uppercase tracking-tight">{{ $card['label'] }}</h2>
                    <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-400">{{ $card['blurb'] }}</p>
                    @if ($activeId === $card['id'])
                        <p class="mt-4 text-[11px] font-bold uppercase tracking-widest text-yellow-400">Currently loaded</p>
                    @endif
                    <button type="submit" class="mt-6 border-2 border-slate-950 bg-yellow-400 px-4 py-3 text-xs font-black uppercase tracking-wider text-slate-950 hover:bg-yellow-300">
                        View live demo
                    </button>
                </form>
            @endforeach
        </div>

        <p class="mt-10 text-center text-xs text-slate-500">
            Hub is gated by <code class="text-slate-400">APP_DEMO_HUB</code>. Not for production client sites.
        </p>

        @if (\App\Support\Niche\NicheResolver::demoMode())
            <div class="mt-6 flex justify-center">
                <form method="POST" action="{{ route('demo.reset') }}">
                    @csrf
                    <button type="submit" class="border-2 border-white/20 bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-wider text-white hover:border-yellow-400">
                        Reset current pack
                    </button>
                </form>
            </div>
        @endif
    </div>
</body>
</html>
