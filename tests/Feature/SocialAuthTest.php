<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_social_register_succeeds_with_valid_provider_id(): void
    {
        $providerId = 'google-'.uniqid();

        $response = $this->postJson('/api/v1/customers/auth/social-register', [
            'name' => 'Social User',
            'email' => 'social-register-'.uniqid().'@example.com',
            'gender' => 'male',
            'provider' => 'google',
            'provider_id' => $providerId,
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('customers.customer_registered_successfully'))
            ->assertJsonPath('data.provider', 'google');

        $this->assertDatabaseHas('customers', [
            'email' => $response->json('data.email'),
            'provider' => 'google',
            'provider_id' => $providerId,
        ]);
    }

    public function test_social_register_fails_when_provider_id_is_missing(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/social-register', [
            'name' => 'Social User',
            'email' => 'social-register-'.uniqid().'@example.com',
            'gender' => 'male',
            'provider' => 'google',
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonFragment([__('customers.provider_id_required')]);
    }

    public function test_social_register_fails_when_provider_id_is_too_short(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/social-register', [
            'name' => 'Social User',
            'email' => 'social-register-'.uniqid().'@example.com',
            'gender' => 'male',
            'provider' => 'google',
            'provider_id' => '1234567',
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonFragment([__('customers.provider_id_min')]);
    }

    public function test_social_register_fails_when_provider_id_is_already_taken(): void
    {
        $providerId = 'google-'.uniqid();

        Customer::create([
            'name' => 'Existing Social User',
            'email' => 'existing-social-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'provider' => 'google',
            'provider_id' => $providerId,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/customers/auth/social-register', [
            'name' => 'Another Social User',
            'email' => 'another-social-'.uniqid().'@example.com',
            'gender' => 'female',
            'provider' => 'google',
            'provider_id' => $providerId,
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonFragment([__('customers.provider_id_unique')]);
    }

    public function test_social_login_succeeds_when_credentials_match(): void
    {
        $providerId = 'google-'.uniqid();
        $email = 'social-login-'.uniqid().'@example.com';

        Customer::create([
            'name' => 'Social Login User',
            'email' => $email,
            'password' => 'password',
            'gender' => 'male',
            'provider' => 'google',
            'provider_id' => $providerId,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/customers/auth/social-login', [
            'email' => $email,
            'provider' => 'google',
            'provider_id' => $providerId,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('customers.customer_logged_in_successfully'))
            ->assertJsonPath('data.email', $email)
            ->assertJsonStructure([
                'data' => [
                    'token',
                ],
            ]);
    }

    public function test_social_login_returns_not_found_when_customer_does_not_exist(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/social-login', [
            'email' => 'missing-social-'.uniqid().'@example.com',
            'provider' => 'google',
            'provider_id' => 'google-'.uniqid(),
        ]);

        $response->assertNotFound()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', __('customers.customer_not_found'));
    }

    public function test_social_login_fails_when_provider_id_is_too_short(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/social-login', [
            'email' => 'social-login-'.uniqid().'@example.com',
            'provider' => 'google',
            'provider_id' => '1234567',
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonFragment([__('customers.provider_id_min')]);
    }
}
