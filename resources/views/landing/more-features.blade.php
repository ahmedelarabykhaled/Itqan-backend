@php
    $icons = [
        'browse' => 'M4 6h16M4 12h16M4 18h7',
        'translations' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7m-6.5-2.5L17 9l3.5 6.5M21 21H3',
        'mushaf' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'sync' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        'profile' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'notifications' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1h6z',
    ];
@endphp

<section class="landing-section overflow-hidden">
    <div class="landing-orb bottom-0 end-[-8%] h-80 w-80 animate-float-slow bg-itqan-100/60"></div>

    <div class="landing-container relative">
        <div class="mx-auto max-w-2xl text-center">
            <span class="landing-eyebrow" data-reveal="up">{{ __('landing.more_features.title') }}</span>

            <h2 class="text-3xl font-bold tracking-tight text-itqan-800 sm:text-4xl" data-reveal="up" data-reveal-delay="100">
                {{ __('landing.more_features.title') }}
            </h2>

            <p class="mt-4 text-base text-itqan-600 sm:text-lg" data-reveal="up" data-reveal-delay="180">
                {{ __('landing.more_features.subtitle') }}
            </p>

            <div class="landing-divider mt-8" data-reveal="scale" data-reveal-delay="240"></div>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($icons as $item => $path)
                <article
                    class="landing-card landing-card-hover group p-7"
                    data-reveal="up"
                    data-reveal-delay="{{ 80 * $loop->index }}"
                >
                    <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-itqan-100 text-itqan-600 transition duration-500 group-hover:bg-itqan-600 group-hover:text-white group-hover:shadow-[0_14px_26px_-14px_rgba(15,110,116,0.9)]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="{{ $path }}" />
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold text-itqan-800">{{ __('landing.more_features.items.'.$item.'.title') }}</h3>
                    <p class="mt-2.5 text-sm leading-relaxed text-itqan-600">{{ __('landing.more_features.items.'.$item.'.description') }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
