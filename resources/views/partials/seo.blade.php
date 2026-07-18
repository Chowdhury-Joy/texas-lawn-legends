@php
    $siteName = setting('site_name', 'Texas Lawn Legends');
    $title = ($seoTitle ?? null) ?: setting('meta_title', $siteName);
    $description = ($seoDescription ?? null) ?: setting('meta_description', '');
    $keywords = setting('meta_keywords');
    $ogImage = ($seoImage ?? null) ?: setting_image('og_image');
    $canonical = url()->current();
    $allowIndex = filter_var(setting('robots_index', true), FILTER_VALIDATE_BOOLEAN) && ! ($noindex ?? false);
    $phone = setting('primary_phone');
    $email = setting('primary_email');
    $address = setting('business_address');
    $areas = (array) setting('service_areas', []);
    $favicon = setting_image('favicon');
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
@if ($keywords)<meta name="keywords" content="{{ $keywords }}">@endif
<link rel="canonical" href="{{ $canonical }}">
<meta name="robots" content="{{ $allowIndex ? 'index, follow' : 'noindex, nofollow' }}">

{{-- Open Graph --}}
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
@if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif

{{-- Twitter --}}
<meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
@if ($ogImage)<meta name="twitter:image" content="{{ $ogImage }}">@endif

{{-- Favicon --}}
@if ($favicon)
    <link rel="icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
@else
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' fill='%231b4332'/><text x='50' y='68' font-size='60' font-family='Arial' font-weight='bold' text-anchor='middle' fill='%23facc15'>L</text></svg>">
@endif

{{-- Structured data: LocalBusiness --}}
<script type="application/ld+json">
{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'LandscapingBusiness',
    'name' => $siteName,
    'description' => $description ?: null,
    'url' => url('/'),
    'telephone' => $phone ?: null,
    'email' => $email ?: null,
    'image' => $ogImage ?: null,
    'address' => $address ? [
        '@type' => 'PostalAddress',
        'streetAddress' => $address,
        'addressLocality' => 'Dallas',
        'addressRegion' => 'TX',
        'addressCountry' => 'US',
    ] : null,
    'areaServed' => ! empty($areas) ? array_values(array_map(fn ($a) => [
        '@type' => 'Place',
        'name' => $a,
    ], $areas)) : null,
], fn ($v) => $v !== null), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
