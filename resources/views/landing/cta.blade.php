<section id="download" class="landing-section overflow-hidden">
    <div class="landing-container">
        <div
            class="relative mx-auto max-w-5xl overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-itqan-700 via-itqan-600 to-itqan-500 px-8 py-16 text-center shadow-[0_40px_80px_-40px_rgba(9,68,71,0.7)] md:px-16 md:py-20"
            data-reveal="scale"
        >
            <div class="landing-orb -top-24 start-[-10%] h-72 w-72 animate-pulse-glow bg-white/20"></div>
            <div class="landing-orb -bottom-24 end-[-10%] h-72 w-72 animate-float-slow bg-itqan-200/30"></div>
            <div class="absolute inset-0 animate-spin-slow bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.16),transparent_55%)]"></div>

            <div class="relative">
                <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">{{ __('landing.cta.title') }}</h2>

                <p class="mx-auto mt-5 max-w-2xl text-base text-itqan-100 sm:text-lg">{{ __('landing.cta.subtitle') }}</p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ $storeLinks['app_store'] }}" class="landing-btn-primary !bg-white !text-itqan-700 hover:!bg-itqan-50">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                        </svg>
                        {{ __('landing.hero.download_app_store') }}
                    </a>

                    <a href="{{ $storeLinks['play_store'] }}" class="landing-btn-outline !border-white/40 !bg-white/10 !text-white hover:!bg-white/20">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M3.609 1.814 13.792 12 3.61 22.186a1.016 1.016 0 0 1-.557-.276 1.016 1.016 0 0 1-.276-.557V2.647c0-.209.099-.406.276-.557a1.016 1.016 0 0 1 .557-.276zm10.89 10.893 2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.198 2.807 1.626a1.016 1.016 0 0 1 0 1.762l-2.807 1.626L15.206 12l2.492-2.491zM5.864 3.458l10.937 6.333-2.302 2.302L5.864 3.458z"/>
                        </svg>
                        {{ __('landing.hero.download_play_store') }}
                    </a>
                </div>

                <p class="mt-6 text-sm font-medium text-itqan-100/90">{{ __('landing.cta.free') }}</p>
            </div>
        </div>
    </div>
</section>
