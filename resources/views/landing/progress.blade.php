<section id="progress" class="landing-section overflow-hidden bg-white">
    <div class="landing-orb top-16 start-[-10%] h-96 w-96 animate-float-slow bg-itqan-100/50"></div>

    <div class="landing-container relative">
        <div class="grid items-center gap-14 lg:grid-cols-2 lg:gap-16">
            <div>
                <span class="landing-eyebrow" data-reveal="up">{{ __('landing.progress.stats.mastery') }}</span>

                <h2 class="text-3xl font-bold tracking-tight text-itqan-800 sm:text-4xl" data-reveal="up" data-reveal-delay="100">
                    {{ __('landing.progress.title') }}
                </h2>

                <p class="mt-4 text-lg font-medium text-itqan-600" data-reveal="up" data-reveal-delay="180">
                    {{ __('landing.progress.subtitle') }}
                </p>

                <p class="mt-5 text-base leading-relaxed text-itqan-600" data-reveal="up" data-reveal-delay="260">
                    {{ __('landing.progress.body') }}
                </p>

                <div class="mt-10 grid gap-4 sm:grid-cols-[auto_1fr] sm:items-center">
                    <div class="landing-card flex items-center justify-center p-5" data-reveal="scale" data-reveal-delay="300">
                        <div class="relative h-32 w-32">
                            <svg class="h-full w-full -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                                <circle class="landing-ring-track" cx="50" cy="50" r="42" fill="none" stroke-width="9" />
                                <circle
                                    class="landing-ring-progress"
                                    cx="50"
                                    cy="50"
                                    r="42"
                                    fill="none"
                                    stroke-width="9"
                                    stroke-linecap="round"
                                    style="--ring-circumference: 264; --ring-offset: 0"
                                />
                            </svg>

                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-2xl font-bold text-itqan-700" data-counter="100" data-counter-suffix="%"></span>
                                <span class="text-[11px] font-medium text-itqan-500">{{ __('landing.progress.stats.mastery') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="landing-card landing-card-hover p-5" data-reveal="up" data-reveal-delay="360">
                            <p class="text-xs font-medium text-itqan-500">{{ __('landing.progress.stats.time') }}</p>
                            <p class="mt-2 text-xl font-bold text-itqan-700">
                                <span data-counter="5"></span>
                                <span class="text-sm font-semibold">{{ __('landing.progress.minutes') }}</span>
                            </p>
                        </div>

                        <div class="landing-card landing-card-hover p-5" data-reveal="up" data-reveal-delay="440">
                            <p class="text-xs font-medium text-itqan-500">{{ __('landing.progress.stats.repetitions') }}</p>
                            <p class="mt-2 text-xl font-bold text-itqan-700">
                                <span data-counter="10" data-counter-suffix="/10"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <a href="#download" class="landing-btn-primary mt-10" data-reveal="up" data-reveal-delay="500">
                    {{ __('landing.progress.cta') }}
                </a>
            </div>

            <div class="flex justify-center lg:justify-end" data-reveal="end">
                <div class="relative">
                    <div class="landing-orb -inset-8 animate-pulse-glow bg-gradient-to-br from-itqan-200/60 to-white"></div>
                    <div class="absolute -inset-6 -z-10 rounded-[3.5rem] border border-itqan-100"></div>

                    <div class="animate-float" style="animation-delay: 1.6s">
                        <div class="landing-phone-frame relative">
                            <div class="landing-phone-notch"></div>
                            <img
                                src="{{ asset('images/app/session.png') }}"
                                alt="{{ __('landing.progress.title') }}"
                                class="block w-full"
                                width="286"
                                height="580"
                                loading="lazy"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
