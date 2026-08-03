<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\UserMemorizedAyah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemorizedAyahClearHistoryTest extends TestCase
{
    use DatabaseTransactions;

    private Customer $customer;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'name' => 'Clear History Test User',
            'email' => 'clear-history-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $this->token = $this->customer->createToken('auth-token')->plainTextToken;
    }

    public function test_clear_memorized_history_deletes_all_records_for_authenticated_customer(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 2,
            'ayah_number' => 255,
            'memorized_at' => now(),
            'statuses' => ['bookmarked'],
        ]);

        $response = $this->deleteJson('/api/v1/customers/memorized', [], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('messages.memorized_history_cleared_successfully'));

        $this->assertDatabaseMissing('user_memorized_ayahs', [
            'user_id' => $this->customer->id,
        ]);
    }

    public function test_clear_memorized_history_does_not_affect_other_customers(): void
    {
        $otherCustomer = Customer::create([
            'name' => 'Other Customer',
            'email' => 'other-customer-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        $otherRecord = UserMemorizedAyah::query()->create([
            'user_id' => $otherCustomer->id,
            'surah_id' => 1,
            'ayah_number' => 2,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        $response = $this->deleteJson('/api/v1/customers/memorized', [], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('user_memorized_ayahs', [
            'user_id' => $this->customer->id,
        ]);

        $this->assertDatabaseHas('user_memorized_ayahs', [
            'id' => $otherRecord->id,
            'user_id' => $otherCustomer->id,
        ]);
    }

    public function test_clear_memorized_history_fails_when_unauthenticated(): void
    {
        $response = $this->deleteJson('/api/v1/customers/memorized');

        $response->assertUnauthorized();
    }

    public function test_clear_memorized_history_succeeds_when_no_records_exist(): void
    {
        $response = $this->deleteJson('/api/v1/customers/memorized', [], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('messages.memorized_history_cleared_successfully'));

        $this->assertDatabaseMissing('user_memorized_ayahs', [
            'user_id' => $this->customer->id,
        ]);
    }
}
