<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_renders_successfully_with_arabic_as_default(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee(__('landing.hero.title', [], 'ar'), false)
            ->assertSee('dir="rtl"', false)
            ->assertSee('images/app/home.png', false);
    }

    public function test_landing_page_renders_in_english_when_requested(): void
    {
        $response = $this->get('/?lang=en');

        $response
            ->assertOk()
            ->assertSee(__('landing.hero.title', [], 'en'), false)
            ->assertSee('dir="ltr"', false);
    }

    public function test_landing_page_includes_core_sections(): void
    {
        $response = $this->get('/?lang=ar');

        $response
            ->assertOk()
            ->assertSee(__('landing.why.title', [], 'ar'), false)
            ->assertSee(__('landing.features.title', [], 'ar'), false)
            ->assertSee(__('landing.faq.title', [], 'ar'), false)
            ->assertSee(__('landing.cta.title', [], 'ar'), false)
            ->assertSee('images/app/surahs.png', false)
            ->assertSee('images/app/session.png', false);
    }

    public function test_landing_page_includes_store_download_buttons(): void
    {
        $response = $this->get('/?lang=en');

        $response
            ->assertOk()
            ->assertSee(__('landing.hero.download_app_store', [], 'en'), false)
            ->assertSee(__('landing.hero.download_play_store', [], 'en'), false);
    }
}
