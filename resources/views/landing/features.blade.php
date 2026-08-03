<section id="features" class="landing-section overflow-hidden">
    <div class="landing-orb top-10 end-[-6%] h-72 w-72 animate-float-slow bg-itqan-100/60"></div>

    <div class="landing-container relative">
        <div class="mx-auto max-w-2xl text-center">
            <span class="landing-eyebrow" data-reveal="up">{{ __('landing.nav.features') }}</span>

            <h2 class="text-3xl font-bold tracking-tight text-itqan-800 sm:text-4xl" data-reveal="up" data-reveal-delay="100">
                {{ __('landing.features.title') }}
            </h2>

            <p class="mt-4 text-base text-itqan-600 sm:text-lg" data-reveal="up" data-reveal-delay="180">
                {{ __('landing.features.subtitle') }}
            </p>

            <div class="landing-divider mt-8" data-reveal="scale" data-reveal-delay="240"></div>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach (['reading', 'flashcards', 'memorization'] as $index => $feature)
                <article
                    class="landing-gradient-card group p-8 hover:-translate-y-2"
                    data-reveal="up"
                    data-reveal-delay="{{ 120 * $index }}"
                >
                    <div class="relative">
                        <div class="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-itqan-500 to-itqan-700 text-white shadow-[0_16px_28px_-16px_rgba(15,110,116,0.9)] transition duration-500 group-hover:scale-110 group-hover:rotate-3">
                            @if ($feature === 'reading')
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            @elseif ($feature === 'flashcards')
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            @else
                                <svg class="h-7 w-7 transition duration-700 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-itqan-800">{{ __('landing.features.'.$feature.'.title') }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-itqan-600">{{ __('landing.features.'.$feature.'.description') }}</p>

                        <span class="mt-6 inline-flex items-center gap-1.5 text-xs font-semibold text-itqan-500 opacity-0 transition duration-500 group-hover:opacity-100">
                            {{ __('landing.why.cta') }}
                            <svg class="h-3.5 w-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
