<footer class="relative overflow-hidden bg-itqan-800 text-itqan-100">
    <div class="landing-orb -top-32 start-1/4 h-72 w-72 bg-itqan-600/40"></div>

    <div class="landing-container relative py-16">
        <div class="grid gap-12 md:grid-cols-3">
            <div data-reveal="up">
                <div class="flex items-center gap-2.5 text-lg font-bold text-white">
                    <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-itqan-500 to-itqan-700 text-sm font-bold">إ</span>
                    <span class="tracking-tight">{{ config('app.name', 'Itqan') }}</span>
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed text-itqan-200">{{ __('landing.footer.tagline') }}</p>
            </div>

            <div data-reveal="up" data-reveal-delay="100">
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">{{ __('landing.footer.policies') }}</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach (['terms' => 'terms-modal', 'privacy' => 'privacy-modal', 'delete_account' => 'delete-account-modal'] as $key => $modalId)
                        <li>
                            <button
                                type="button"
                                data-modal-open="{{ $modalId }}"
                                class="group inline-flex items-center gap-2 text-itqan-200 transition duration-300 hover:text-white"
                            >
                                <span class="h-px w-0 bg-itqan-300 transition-all duration-300 group-hover:w-4"></span>
                                {{ __('landing.footer.'.$key) }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div data-reveal="up" data-reveal-delay="180">
                <h3 class="text-sm font-semibold tracking-wide text-white uppercase">{{ __('landing.footer.contact') }}</h3>
                <p class="mt-5">
                    <a
                        href="mailto:{{ __('landing.footer.email') }}"
                        class="inline-flex items-center gap-2 text-sm text-itqan-200 transition duration-300 hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ __('landing.footer.email') }}
                    </a>
                </p>
            </div>
        </div>

        <div class="mt-14 border-t border-itqan-700/70 pt-7 text-center text-xs text-itqan-300">
            &copy; {{ date('Y') }} {{ config('app.name', 'Itqan') }}. {{ __('landing.footer.copyright') }}
        </div>
    </div>
</footer>

@foreach (['terms', 'privacy', 'delete_account'] as $modal)
    <div
        id="{{ str_replace('_', '-', $modal) }}-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-itqan-900/60 p-4 backdrop-blur-sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ str_replace('_', '-', $modal) }}-title"
    >
        <div class="landing-card landing-modal-panel max-h-[85vh] w-full max-w-lg overflow-y-auto p-7 md:p-9">
            <div class="flex items-start justify-between gap-4">
                <h2 id="{{ str_replace('_', '-', $modal) }}-title" class="text-xl font-bold text-itqan-800">
                    {{ __('landing.'.$modal.'.title') }}
                </h2>
                <button
                    type="button"
                    data-modal-close
                    class="rounded-xl p-1.5 text-itqan-500 transition duration-300 hover:rotate-90 hover:bg-itqan-50 hover:text-itqan-700"
                    aria-label="{{ __('landing.footer.close') }}"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="landing-divider mt-5 !mx-0 !w-16"></div>

            <p class="mt-5 text-sm leading-relaxed text-itqan-600">{{ __('landing.'.$modal.'.content') }}</p>
        </div>
    </div>
@endforeach
