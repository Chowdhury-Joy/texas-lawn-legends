<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Start your {{ $durationDays }}-day trial — Getwebfield</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-body type-body-md text-content-main antialiased">
    <main class="layout-container space-section">
        <div class="mx-auto max-w-lg">
            <p class="type-tagline text-content-subtle">Getwebfield trial</p>
            <h1 class="type-h2 mt-[var(--space-md)] text-content-main">Create your account</h1>
            <p class="type-body-md mt-[var(--space-sm)] text-content-base">
                {{ $durationDays }} days of full access. Password stays visible while you type — no confirm field.
            </p>

            @if ($errors->any())
                <div class="mt-[var(--space-lg)] border border-black bg-white p-[var(--space-md)]" role="alert">
                    <ul class="type-body-sm text-content-main list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('trial.signup.store') }}" class="mt-[var(--space-xl)] stack-header">
                @csrf
                <div>
                    <label for="name" class="type-tagline text-content-subtle">Your name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name"
                           class="mt-[var(--space-xs)] w-full border border-black bg-white px-[var(--space-md)] py-[var(--space-sm)] type-body-md text-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                </div>
                <div>
                    <label for="email" class="type-tagline text-content-subtle">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email"
                           class="mt-[var(--space-xs)] w-full border border-black bg-white px-[var(--space-md)] py-[var(--space-sm)] type-body-md text-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                </div>
                <div>
                    <label for="password" class="type-tagline text-content-subtle">Password</label>
                    <input id="password" name="password" type="text" value="{{ old('password') }}" required autocomplete="new-password"
                           class="mt-[var(--space-xs)] w-full border border-black bg-white px-[var(--space-md)] py-[var(--space-sm)] type-body-md text-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                </div>
                <div>
                    <label for="business_slug" class="type-tagline text-content-subtle">Short business handle (optional)</label>
                    <input id="business_slug" name="business_slug" type="text" value="{{ old('business_slug') }}" autocomplete="organization"
                           placeholder="acme-roofing"
                           class="mt-[var(--space-xs)] w-full border border-black bg-white px-[var(--space-md)] py-[var(--space-sm)] type-body-md text-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                </div>
                <button type="submit" class="type-tagline w-full border border-black bg-black px-[var(--space-xl)] py-[var(--space-md)] text-content-inverted-main hover:bg-content-main focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    Continue — pick industry
                </button>
            </form>

            @if ($googleReady)
                <p class="type-body-sm mt-[var(--space-xl)] text-center text-content-subtle">or</p>
                <a href="{{ route('auth.google') }}" class="type-tagline mt-[var(--space-md)] flex w-full items-center justify-center border border-black bg-white px-[var(--space-xl)] py-[var(--space-md)] text-content-main hover:bg-content-disabled/30 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    Continue with Google
                </a>
            @endif

            <p class="type-body-sm mt-[var(--space-xl)] text-content-subtle">
                <a href="{{ route('agency.home') }}" class="underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">Back to Getwebfield</a>
            </p>
        </div>
    </main>
</body>
</html>
