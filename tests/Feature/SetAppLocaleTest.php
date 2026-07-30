<?php

namespace Tests\Feature;

use Tests\TestCase;

class SetAppLocaleTest extends TestCase
{
    public function test_sets_arabic_locale_from_accept_language_header(): void
    {
        $this->withHeader('Accept-Language', 'ar')
            ->postJson('/api/v1/customers/auth/login')
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'خطأ في البيانات المدخلة']);

        $this->assertEquals('ar', app()->getLocale());
    }

    public function test_sets_english_locale_from_accept_language_header(): void
    {
        $this->withHeader('Accept-Language', 'en')
            ->postJson('/api/v1/customers/auth/login')
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Validation error']);

        $this->assertEquals('en', app()->getLocale());
    }

    public function test_sets_locale_from_query_parameter(): void
    {
        $this->postJson('/api/v1/customers/auth/login?lang=ar')
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'خطأ في البيانات المدخلة']);

        $this->assertEquals('ar', app()->getLocale());
    }
}
