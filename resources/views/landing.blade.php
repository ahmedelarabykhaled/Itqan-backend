@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $storeLinks = [
        'app_store' => '#',
        'play_store' => '#',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ __('landing.meta.description') }}">

    <title>{{ __('landing.meta.title') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|tajawal:400,500,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    @include('landing.nav', ['storeLinks' => $storeLinks])

    <main>
        @include('landing.hero', ['storeLinks' => $storeLinks])
        @include('landing.why')
        @include('landing.features')
        @include('landing.progress')
        @include('landing.more-features')
        @include('landing.faq')
        @include('landing.cta', ['storeLinks' => $storeLinks])
    </main>

    @include('landing.footer')
</body>
</html>
