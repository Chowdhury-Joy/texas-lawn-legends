<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Pick your industry — Getwebfield trial</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-body type-body-md text-content-main antialiased">
    <main class="layout-container space-section">
        <div class="stack-header max-w-3xl">
            <p class="type-tagline text-content-subtle">Step 2 of 2</p>
            <h1 class="type-h2 text-content-main">Choose your industry skin</h1>
            <p class="type-body-lg text-content-base">
                One industry per account. We load a finished model home with Part 3 (Website + Booking + Ops). You get {{ $durationDays }} days of full admin access with a demo banner on the public site.
            </p>
        </div>

        @if ($errors->any())
            <div class="mt-[var(--space-lg)] border border-black bg-white p-[var(--space-md)]" role="alert">
                <ul class="type-body-sm text-content-main list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid-cards mt-[var(--space-xl)]" data-card-count="{{ count($cards) }}">
            @foreach ($cards as $card)
                <form method="POST" action="{{ route('trial.niche.store') }}" class="flex flex-col border border-black p-[var(--space-xl)]">
                    @csrf
                    <input type="hidden" name="niche" value="{{ $card['id'] }}">
                    <h2 class="type-h4 text-content-main">{{ $card['label'] }}</h2>
                    <p class="type-body-md mt-[var(--space-sm)] flex-1 text-content-base">{{ $card['blurb'] }}</p>
                    <button type="submit" class="type-tagline mt-[var(--space-xl)] border border-black bg-black px-[var(--space-lg)] py-[var(--space-sm)] text-content-inverted-main hover:bg-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Start with {{ $card['label'] }}
                    </button>
                </form>
            @endforeach
        </div>
    </main>
</body>
</html>
