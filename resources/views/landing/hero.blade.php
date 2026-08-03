<section id="home" class="landing-section overflow-hidden pt-10 md:pt-16">
    <div class="landing-orb -top-24 start-[-10%] h-[420px] w-[420px] animate-pulse-glow bg-itqan-200/50"></div>
    <div class="landing-orb top-40 end-[-12%] h-[380px] w-[380px] animate-float-slow bg-itqan-100/70"></div>

    <div class="landing-container relative">
        <div class="grid items-center gap-14 lg:grid-cols-2 lg:gap-16">
            <div class="order-2 lg:order-1">
                <span class="landing-eyebrow animate-rise" style="--reveal-delay: 60ms">
                    {{ __('landing.hero.badge') }}
                </span>

                <h1 class="animate-rise text-4xl font-bold leading-[1.15] tracking-tight sm:text-5xl lg:text-6xl" style="--reveal-delay: 160ms">
                    <span class="text-gradient">{{ __('landing.hero.title') }}</span>
                </h1>

                <p class="animate-rise mt-6 max-w-xl text-base leading-relaxed text-itqan-600 sm:text-lg" style="--reveal-delay: 280ms">
                    {{ __('landing.hero.subtitle') }}
                </p>

                <div class="animate-rise mt-9 flex flex-wrap items-center gap-3" style="--reveal-delay: 400ms">
                    <a href="{{ $storeLinks['app_store'] }}" class="landing-btn-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                        </svg>
                        {{ __('landing.hero.download_app_store') }}
                    </a>

                    <a href="{{ $storeLinks['play_store'] }}" class="landing-btn-outline">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M3.609 1.814 13.792 12 3.61 22.186a1.016 1.016 0 0 1-.557-.276 1.016 1.016 0 0 1-.276-.557V2.647c0-.209.099-.406.276-.557a1.016 1.016 0 0 1 .557-.276zm10.89 10.893 2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.198 2.807 1.626a1.016 1.016 0 0 1 0 1.762l-2.807 1.626L15.206 12l2.492-2.491zM5.864 3.458l10.937 6.333-2.302 2.302L5.864 3.458z"/>
                        </svg>
                        {{ __('landing.hero.download_play_store') }}
                    </a>
                </div>

                <p class="animate-rise mt-5 text-sm font-medium text-itqan-500" style="--reveal-delay: 480ms">
                    {{ __('landing.hero.free') }}
                </p>

                <dl class="animate-rise mt-10 grid max-w-md grid-cols-3 gap-4 border-t border-itqan-100 pt-7" style="--reveal-delay: 560ms">
                    @foreach (['surahs' => 114, 'juz' => 30, 'paths' => 3] as $stat => $value)
                        <div>
                            <dt class="sr-only">{{ __('landing.hero.stats.'.$stat) }}</dt>
                            <dd>
                                <span class="block text-2xl font-bold text-itqan-700" data-counter="{{ $value }}">{{ $value }}</span>
                                <span class="mt-1 block text-xs font-medium text-itqan-500">{{ __('landing.hero.stats.'.$stat) }}</span>
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="order-1 flex justify-center lg:order-2">
                <div class="relative animate-rise" style="--reveal-delay: 220ms">
                    <div class="landing-orb -inset-10 animate-pulse-glow bg-gradient-to-br from-itqan-200/70 to-itqan-100/40"></div>

                    <div class="absolute -inset-6 -z-10 rounded-[3.5rem] border border-itqan-200/50"></div>

                    <div class="animate-float">
                        <div class="landing-phone-frame relative">
                            <div class="landing-phone-notch"></div>
                            <img
                                src="{{ asset('images/app/home.png') }}"
                                alt="{{ __('landing.hero.title') }}"
                                class="block w-full"
                                width="286"
                                height="580"
                                fetchpriority="high"
                            >
                        </div>
                    </div>

                    <div class="landing-float-badge -top-2 start-[-12%] animate-float" style="animation-delay: 1.2s">
                        <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-itqan-100 text-itqan-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        {{ __('landing.hero.badges.tracked') }}
                    </div>

                    <div class="landing-float-badge bottom-10 end-[-14%] animate-float" style="animation-delay: 2.4s">
                        <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-itqan-100 text-itqan-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </span>
                        {{ __('landing.hero.badges.fast') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
