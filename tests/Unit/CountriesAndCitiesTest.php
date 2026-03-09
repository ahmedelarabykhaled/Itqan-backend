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
        $response = $this->get('/api/v1/locations/countries')->assertStatus(200);

        $countries = $response->json()['data'];

        $this->assertCount(250, $countries);

        $this->assertContains('Egypt', array_column($countries, 'name'));

        $this->assertContains('Saudi Arabia', array_column($countries, 'name'));

        $this->assertContains('United Arab Emirates', array_column($countries, 'name'));
    }

    public function test_cities_by_country_id(): void
    {
        $response = $this->get('/api/v1/locations/countries/65/cities')->assertStatus(200);

        $cities = $response->json()['data'];

        $this->assertCount(157, $cities);
    }
}
