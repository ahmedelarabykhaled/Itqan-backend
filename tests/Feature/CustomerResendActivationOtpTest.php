<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Notifications\ActivateAccountOtpNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CustomerResendActivationOtpTest extends TestCase
{
    use DatabaseTransactions;

    public function test_resend_activation_otp_successfully(): void
    {
        Notification::fake();

        $customer = Customer::create([
            'name' => 'Resend Otp User',
            'email' => 'resend-otp-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'verification_code' => '111111',
            'verification_code_expires_at' => now()->subMinute(),
        ]);

        $response = $this->postJson('/api/v1/customers/auth/resend-activation-otp', [
            'email' => $customer->email,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $customer->email);

        $customer->refresh();

        $this->assertSame('123456', (string) $customer->verification_code);
        $this->assertNotNull($customer->verification_code_expires_at);
        $this->assertTrue(
            \Carbon\Carbon::parse($customer->verification_code_expires_at)->isFuture()
        );

        Notification::assertSentTo($customer, ActivateAccountOtpNotification::class);
    }

    public function test_resend_activation_otp_fails_when_customer_not_found(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/resend-activation-otp', [
            'email' => 'missing-'.uniqid().'@example.com',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_resend_activation_otp_fails_when_already_verified(): void
    {
        Notification::fake();

        $customer = Customer::create([
            'name' => 'Verified User',
            'email' => 'verified-otp-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/customers/auth/resend-activation-otp', [
            'email' => $customer->email,
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);

        Notification::assertNothingSent();
    }

    public function test_resend_activation_otp_validation_error(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/resend-activation-otp', []);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
