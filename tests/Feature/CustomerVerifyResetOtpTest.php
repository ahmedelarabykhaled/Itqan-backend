<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerVerifyResetOtpTest extends TestCase
{
    use DatabaseTransactions;

    public function test_verify_reset_otp_successfully(): void
    {
        $customer = Customer::create([
            'name' => 'OTP Verify User',
            'email' => 'otp-verify-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'gender' => 'male',
        ]);

        $otp = '123456';
        DB::table('password_reset_tokens')->insert([
            'email' => $customer->email,
            'token' => Hash::make($otp),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/customers/auth/verify-reset-otp', [
            'email' => $customer->email,
            'token' => $otp,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', $customer->email)
            ->assertJsonPath('data.valid', true);
    }

    public function test_verify_reset_otp_fails_when_customer_not_found(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/verify-reset-otp', [
            'email' => 'nonexistent-'.uniqid().'@example.com',
            'token' => '123456',
        ]);

        $response->assertNotFound()
            ->assertJsonPath('success', false);
    }

    public function test_verify_reset_otp_fails_when_token_invalid(): void
    {
        $customer = Customer::create([
            'name' => 'OTP Verify User 2',
            'email' => 'otp-verify2-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'gender' => 'male',
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => $customer->email,
            'token' => Hash::make('123456'),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/v1/customers/auth/verify-reset-otp', [
            'email' => $customer->email,
            'token' => '654321',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_verify_reset_otp_fails_when_token_expired(): void
    {
        $customer = Customer::create([
            'name' => 'OTP Verify User 3',
            'email' => 'otp-verify3-'.uniqid().'@example.com',
            'password' => Hash::make('password'),
            'gender' => 'male',
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => $customer->email,
            'token' => Hash::make('123456'),
            'created_at' => now()->subMinutes(61),
        ]);

        $response = $this->postJson('/api/v1/customers/auth/verify-reset-otp', [
            'email' => $customer->email,
            'token' => '123456',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    public function test_verify_reset_otp_validation_error(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/verify-reset-otp', []);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
