<section id="why" class="landing-section overflow-hidden bg-white">
    <div class="landing-orb bottom-0 start-[-8%] h-80 w-80 bg-itqan-100/60"></div>

    <div class="landing-container relative">
        <div class="grid items-center gap-14 lg:grid-cols-2 lg:gap-16">
            <div class="flex justify-center lg:justify-start" data-reveal="start">
                <div class="relative">
                    <div class="landing-orb -inset-8 animate-pulse-glow bg-gradient-to-br from-itqan-200/60 to-white"></div>
                    <div class="absolute -inset-6 -z-10 rounded-[3.5rem] border border-itqan-100"></div>

                    <div class="animate-float" style="animation-delay: 0.8s">
                        <div class="landing-phone-frame relative">
                            <div class="landing-phone-notch"></div>
                            <img
                                src="{{ asset('images/app/surahs.png') }}"
                                alt="{{ __('landing.why.title') }}"
                                class="block w-full"
                                width="286"
                                height="580"
                                loading="lazy"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <span class="landing-eyebrow" data-reveal="up">{{ __('landing.nav.why') }}</span>

                <h2 class="text-3xl font-bold tracking-tight text-itqan-800 sm:text-4xl" data-reveal="up" data-reveal-delay="100">
                    {{ __('landing.why.title') }}
                </h2>

                <p class="mt-4 text-lg font-medium text-itqan-600" data-reveal="up" data-reveal-delay="180">
                    {{ __('landing.why.subtitle') }}
                </p>

                <p class="mt-5 text-base leading-relaxed text-itqan-600" data-reveal="up" data-reveal-delay="260">
                    {{ __('landing.why.body') }}
                </p>

                <a href="#download" class="landing-btn-primary mt-9" data-reveal="up" data-reveal-delay="340">
                    {{ __('landing.why.cta') }}
                </a>
            </div>
        </div>
    </div>
</section>
