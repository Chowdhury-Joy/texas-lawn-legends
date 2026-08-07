<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index, follow">
    <title>Getwebfield — Websites &amp; ops for home-service businesses</title>
    <meta name="description" content="Try a full Website + Booking + Ops sandbox for {{ $durationDays }} days. Pick your industry, explore the admin, convert when you are ready.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-body type-body-md text-content-main antialiased">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:border-2 focus:border-black focus:bg-white focus:px-4 focus:py-2 focus:type-tagline focus:text-content-main">Skip to content</a>

    <header class="border-b border-content-disabled">
        <div class="layout-container space-inline flex items-center justify-between py-[var(--space-lg)]">
            <p class="type-h4 text-content-main">Getwebfield</p>
            <a href="{{ route('trial.signup') }}" class="type-tagline border border-black bg-black px-[var(--space-lg)] py-[var(--space-sm)] text-content-inverted-main hover:bg-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                Start {{ $durationDays }}-day trial
            </a>
        </div>
    </header>

    <main id="main-content">
        <section class="bg-white">
            <div class="layout-container space-section">
                <div class="stack-header max-w-3xl">
                    <p class="type-tagline text-content-subtle">For lawn, cleaning, roofing &amp; more</p>
                    <h1 class="type-h1 text-content-main">Your industry website, booking, and ops — ready to try</h1>
                    <p class="type-body-lg text-content-base">
                        Getwebfield is a practice sandbox for home-service owners. Sign up, pick one industry skin, and run the full product for {{ $durationDays }} days. This is not your public brand URL — it is a demo on our hosting so you can decide before you buy.
                    </p>
                </div>
                <div class="mt-[var(--space-xl)] flex flex-wrap gap-[var(--space-md)]">
                    <a href="{{ route('trial.signup') }}" class="type-tagline inline-flex border border-black bg-black px-[var(--space-xl)] py-[var(--space-md)] text-content-inverted-main hover:bg-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Start {{ $durationDays }}-day trial
                    </a>
                    @if ($googleReady)
                        <a href="{{ route('auth.google') }}" class="type-tagline inline-flex border border-black bg-white px-[var(--space-xl)] py-[var(--space-md)] text-content-main hover:bg-content-disabled/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                            Continue with Google
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="bg-black">
            <div class="layout-container space-section">
                <div class="stack-header max-w-3xl">
                    <p class="type-tagline text-content-inverted-subtle">How the trial works</p>
                    <h2 class="type-h2 text-content-inverted-main">Three steps. Full access. Timer on the door.</h2>
                </div>
                <div class="grid-cards mt-[var(--space-xl)]" data-card-count="3">
                    <article class="border border-content-inverted-disabled p-[var(--space-xl)]">
                        <p class="type-tagline text-content-inverted-subtle">01</p>
                        <h3 class="type-h4 mt-[var(--space-md)] text-content-inverted-main">Create an account</h3>
                        <p class="type-body-md mt-[var(--space-sm)] text-content-inverted-base">Email and password, or Google. No card. No confirm-password hoop.</p>
                    </article>
                    <article class="border border-content-inverted-disabled p-[var(--space-xl)]">
                        <p class="type-tagline text-content-inverted-subtle">02</p>
                        <h3 class="type-h4 mt-[var(--space-md)] text-content-inverted-main">Pick your industry</h3>
                        <p class="type-body-md mt-[var(--space-sm)] text-content-inverted-base">One skin per account — lawn, cleaning, roofing, and more. We load a finished model home.</p>
                    </article>
                    <article class="border border-content-inverted-disabled p-[var(--space-xl)]">
                        <p class="type-tagline text-content-inverted-subtle">03</p>
                        <h3 class="type-h4 mt-[var(--space-md)] text-content-inverted-main">Use everything for {{ $durationDays }} days</h3>
                        <p class="type-body-md mt-[var(--space-sm)] text-content-inverted-base">Admin is fully open. A demo banner stays on the public site. Day {{ $durationDays + 1 }} locks admin login.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="layout-container space-section">
                <div class="stack-header max-w-3xl">
                    <p class="type-tagline text-content-subtle">After the trial</p>
                    <h2 class="type-h2 text-content-main">Buy means a fresh install — not this sandbox</h2>
                    <p class="type-body-lg text-content-base">
                        When you convert, we start a clean Track A or Track B site for your real customers. Trial leads and edits stay in the sandbox. Your real domain and brand come later.
                    </p>
                </div>
                <div class="mt-[var(--space-xl)]">
                    <a href="{{ route('trial.signup') }}" class="type-tagline inline-flex border border-black bg-black px-[var(--space-xl)] py-[var(--space-md)] text-content-inverted-main hover:bg-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                        Start {{ $durationDays }}-day trial
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-content-disabled">
        <div class="layout-container space-inline py-[var(--space-xl)]">
            <p class="type-body-sm text-content-subtle">Getwebfield — practice sandbox for home-service growth systems.</p>
        </div>
    </footer>
</body>
</html>
