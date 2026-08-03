<section id="faq" class="landing-section overflow-hidden bg-white">
    <div class="landing-orb top-24 start-[-6%] h-72 w-72 bg-itqan-100/50"></div>

    <div class="landing-container relative">
        <div class="mx-auto max-w-2xl text-center">
            <span class="landing-eyebrow" data-reveal="up">{{ __('landing.nav.faq') }}</span>

            <h2 class="text-3xl font-bold tracking-tight text-itqan-800 sm:text-4xl" data-reveal="up" data-reveal-delay="100">
                {{ __('landing.faq.title') }}
            </h2>

            <p class="mt-4 text-base text-itqan-600 sm:text-lg" data-reveal="up" data-reveal-delay="180">
                {{ __('landing.faq.subtitle') }}
            </p>

            <div class="landing-divider mt-8" data-reveal="scale" data-reveal-delay="240"></div>
        </div>

        <div class="mx-auto mt-14 max-w-3xl space-y-4">
            @foreach (__('landing.faq.items') as $index => $item)
                <details
                    class="landing-faq-item landing-card group p-6 hover:border-itqan-200"
                    data-reveal="up"
                    data-reveal-delay="{{ 80 * $index }}"
                >
                    <summary class="cursor-pointer list-none text-base font-semibold text-itqan-800 marker:content-none">
                        <div class="flex items-center justify-between gap-4">
                            <span class="transition duration-300 group-hover:text-itqan-600">{{ $item['question'] }}</span>
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-itqan-50 text-itqan-500 transition duration-500 group-open:rotate-180 group-open:bg-itqan-600 group-open:text-white">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </div>
                    </summary>

                    <p class="landing-faq-answer mt-4 border-t border-itqan-100 pt-4 text-sm leading-relaxed text-itqan-600">
                        {{ $item['answer'] }}
                    </p>
                </details>
            @endforeach
        </div>
    </div>
</section>
