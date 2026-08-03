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

    public function test_clear_status_removes_status_from_all_records_for_authenticated_customer(): void
    {
        $recordWithOnlyMemorized = UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        $recordWithMultipleStatuses = UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 2,
            'ayah_number' => 255,
            'memorized_at' => now(),
            'statuses' => ['memorized', 'bookmarked'],
        ]);

        $recordWithoutMemorized = UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 3,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['bookmarked'],
        ]);

        $response = $this->deleteJson('/api/v1/customers/memorized', [
            'status' => 'memorized',
        ], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('messages.memorized_status_cleared_successfully', [
                'status' => 'memorized',
            ]));

        $this->assertDatabaseMissing('user_memorized_ayahs', [
            'id' => $recordWithOnlyMemorized->id,
        ]);

        $this->assertDatabaseHas('user_memorized_ayahs', [
            'id' => $recordWithMultipleStatuses->id,
            'user_id' => $this->customer->id,
        ]);

        $recordWithMultipleStatuses->refresh();
        $this->assertEqualsCanonicalizing(['bookmarked'], $recordWithMultipleStatuses->statuses);

        $recordWithoutMemorized->refresh();
        $this->assertEqualsCanonicalizing(['bookmarked'], $recordWithoutMemorized->statuses);
    }

    public function test_clear_status_does_not_affect_other_customers(): void
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

        $response = $this->deleteJson('/api/v1/customers/memorized', [
            'status' => 'memorized',
        ], [
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

        $otherRecord->refresh();
        $this->assertEqualsCanonicalizing(['memorized'], $otherRecord->statuses);
    }

    public function test_clear_status_fails_when_unauthenticated(): void
    {
        $response = $this->deleteJson('/api/v1/customers/memorized', [
            'status' => 'memorized',
        ]);

        $response->assertUnauthorized();
    }

    public function test_clear_status_succeeds_when_no_records_have_that_status(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['bookmarked'],
        ]);

        $response = $this->deleteJson('/api/v1/customers/memorized', [
            'status' => 'memorized',
        ], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('messages.memorized_status_cleared_successfully', [
                'status' => 'memorized',
            ]));

        $this->assertDatabaseHas('user_memorized_ayahs', [
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
        ]);
    }

    public function test_clear_status_fails_when_status_is_missing(): void
    {
        $response = $this->deleteJson('/api/v1/customers/memorized', [], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }
}
