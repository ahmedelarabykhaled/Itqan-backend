@php
    $navLinks = [
        '#home' => __('landing.nav.home'),
        '#why' => __('landing.nav.why'),
        '#features' => __('landing.nav.features'),
        '#faq' => __('landing.nav.faq'),
    ];
@endphp

<header class="landing-nav" data-nav>
    <div class="landing-container flex h-18 items-center justify-between gap-4 py-3">
        <a href="#home" class="group flex items-center gap-2.5 text-lg font-bold text-itqan-700">
            <span class="relative flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-itqan-500 to-itqan-700 text-sm font-bold text-white shadow-[0_10px_20px_-10px_rgba(15,110,116,0.9)] transition duration-500 group-hover:scale-105 group-hover:rotate-3">
                إ
            </span>
            <span class="tracking-tight">{{ config('app.name', 'Itqan') }}</span>
        </a>

        <nav class="hidden items-center gap-7 md:flex" aria-label="{{ __('landing.nav.menu') }}">
            @foreach ($navLinks as $href => $label)
                <a href="{{ $href }}" class="landing-nav-link">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            <div class="flex items-center rounded-full border border-itqan-200/80 bg-white/80 p-1 text-xs font-medium backdrop-blur">
                @foreach (['ar', 'en'] as $code)
                    <a
                        href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                        @class([
                            'rounded-full px-3 py-1.5 transition duration-300',
                            'bg-itqan-600 text-white shadow-[0_6px_16px_-8px_rgba(15,110,116,0.9)]' => app()->getLocale() === $code,
                            'text-itqan-700 hover:bg-itqan-50' => app()->getLocale() !== $code,
                        ])
                    >
                        {{ __('landing.locale.'.$code) }}
                    </a>
                @endforeach
            </div>

            <a href="#download" class="landing-btn-primary !px-5 !py-2.5 text-xs">
                {{ __('landing.nav.download') }}
            </a>
        </div>

        <button
            type="button"
            id="mobile-menu-toggle"
            class="inline-flex items-center justify-center rounded-xl border border-itqan-200 bg-white/80 p-2 text-itqan-700 backdrop-blur transition duration-300 hover:border-itqan-300 hover:bg-white md:hidden"
            aria-expanded="false"
            aria-controls="mobile-menu"
            aria-label="{{ __('landing.nav.menu') }}"
        >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="mobile-menu" class="hidden border-t border-itqan-100 bg-white/95 backdrop-blur-xl md:hidden">
        <nav class="landing-container flex flex-col gap-1 py-4" aria-label="{{ __('landing.nav.menu') }}">
            @foreach ($navLinks as $href => $label)
                <a href="{{ $href }}" class="rounded-xl px-3 py-2.5 text-sm font-medium text-itqan-700 transition duration-300 hover:bg-itqan-50 hover:ps-5">
                    {{ $label }}
                </a>
            @endforeach

            <div class="mt-3 flex items-center gap-2 px-3">
                @foreach (['ar', 'en'] as $code)
                    <a
                        href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                        @class([
                            'rounded-full px-3 py-1.5 text-xs font-medium transition duration-300',
                            'bg-itqan-600 text-white' => app()->getLocale() === $code,
                            'border border-itqan-200 text-itqan-700' => app()->getLocale() !== $code,
                        ])
                    >
                        {{ __('landing.locale.'.$code) }}
                    </a>
                @endforeach
            </div>

            <a href="#download" class="landing-btn-primary mx-3 mt-4">{{ __('landing.nav.download') }}</a>
        </nav>
    </div>
</header>
