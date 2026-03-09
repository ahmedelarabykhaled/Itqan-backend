<?php

namespace Tests\Unit;

use Tests\TestCase;

class CountriesAndCitiesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_all_countries(): void
    {
        $response = $this->get('/api/v1/countries')->assertStatus(200);

        $countries = $response->json();

        $this->assertCount(250, $countries);

        $this->assertContains('Egypt', $countries);

        $this->assertContains('Saudi Arabia', $countries);

        $this->assertContains('United Arab Emirates', $countries);
    }
}
