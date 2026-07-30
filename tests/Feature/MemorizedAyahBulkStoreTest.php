<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\UserMemorizedAyah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemorizedAyahBulkStoreTest extends TestCase
{
    use DatabaseTransactions;

    private Customer $customer;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::create([
            'name' => 'Memorized Test User',
            'email' => 'memorized-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        $this->token = $this->customer->createToken('auth-token')->plainTextToken;
    }

    public function test_bulk_store_creates_new_memorized_ayahs_with_multiple_statuses(): void
    {
        $payload = [
            'ayahs' => [
                ['surah_id' => 1, 'ayah_number' => 1, 'statuses' => ['memorized', 'bookmarked', 'saved']],
                ['surah_id' => 1, 'ayah_number' => 2, 'statuses' => ['weak']],
                ['surah_id' => 2, 'ayah_number' => 255, 'statuses' => ['memorized']],
            ],
        ];

        $response = $this->postJson('/api/v1/customers/memorized', $payload, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Ayahs memorized successfully')
            ->assertJsonCount(3, 'data');

        $record1 = UserMemorizedAyah::query()
            ->where('user_id', $this->customer->id)
            ->where('surah_id', 1)
            ->where('ayah_number', 1)
            ->first();

        $this->assertNotNull($record1);
        $this->assertEqualsCanonicalizing(['memorized', 'bookmarked', 'saved'], $record1->statuses);

        $record2 = UserMemorizedAyah::query()
            ->where('user_id', $this->customer->id)
            ->where('surah_id', 1)
            ->where('ayah_number', 2)
            ->first();

        $this->assertNotNull($record2);
        $this->assertEqualsCanonicalizing(['weak'], $record2->statuses);
    }

    public function test_bulk_store_with_statuses_dictionary_flags(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized', 'saved'],
        ]);

        $payload = [
            'ayahs' => [
                [
                    'surah_id' => 1,
                    'ayah_number' => 1,
                    'statuses' => [
                        'bookmarked' => true,
                        'memorized' => false,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/customers/memorized', $payload, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();

        $record = UserMemorizedAyah::query()
            ->where('user_id', $this->customer->id)
            ->where('surah_id', 1)
            ->where('ayah_number', 1)
            ->first();

        $this->assertNotNull($record);
        $this->assertEqualsCanonicalizing(['saved', 'bookmarked'], $record->statuses);
    }

    public function test_bulk_store_deletes_record_when_all_statuses_are_cleared(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        $payload = [
            'ayahs' => [
                [
                    'surah_id' => 1,
                    'ayah_number' => 1,
                    'statuses' => [
                        'memorized' => false,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/customers/memorized', $payload, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();

        $this->assertDatabaseMissing('user_memorized_ayahs', [
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
        ]);
    }

    public function test_bulk_store_with_single_status_string_backward_compatibility(): void
    {
        $payload = [
            'ayahs' => [
                ['surah_id' => 3, 'ayah_number' => 10, 'status' => 'memorized'],
            ],
        ];

        $response = $this->postJson('/api/v1/customers/memorized', $payload, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.surah_id', 3)
            ->assertJsonPath('data.0.ayah_number', 10)
            ->assertJsonPath('data.0.statuses.0', 'memorized');
    }

    public function test_bulk_store_updates_memorized_at_for_existing_ayahs(): void
    {
        $oldTime = now()->subDays(5);

        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => $oldTime,
            'statuses' => ['memorized'],
        ]);

        $payload = [
            'ayahs' => [
                ['surah_id' => 1, 'ayah_number' => 1, 'statuses' => ['memorized', 'bookmarked']],
            ],
        ];

        $response = $this->postJson('/api/v1/customers/memorized', $payload, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk();

        $record = UserMemorizedAyah::query()
            ->where('user_id', $this->customer->id)
            ->where('surah_id', 1)
            ->where('ayah_number', 1)
            ->first();

        $this->assertTrue($record->memorized_at->greaterThan($oldTime));
        $this->assertEqualsCanonicalizing(['memorized', 'bookmarked'], $record->statuses);
    }

    public function test_bulk_store_fails_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/v1/customers/memorized', [
            'ayahs' => [['surah_id' => 1, 'ayah_number' => 1]],
        ]);

        $response->assertUnauthorized();
    }

    public function test_bulk_store_fails_when_ayahs_is_missing(): void
    {
        $response = $this->postJson('/api/v1/customers/memorized', [], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['ayahs']);
    }

    public function test_bulk_store_fails_when_ayahs_is_empty(): void
    {
        $response = $this->postJson('/api/v1/customers/memorized', ['ayahs' => []], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['ayahs']);
    }

    public function test_bulk_store_fails_when_surah_id_is_invalid(): void
    {
        $response = $this->postJson('/api/v1/customers/memorized', [
            'ayahs' => [['surah_id' => 0, 'ayah_number' => 1]],
        ], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['ayahs.0.surah_id']);
    }

    public function test_bulk_store_fails_when_ayah_number_is_missing(): void
    {
        $response = $this->postJson('/api/v1/customers/memorized', [
            'ayahs' => [['surah_id' => 1]],
        ], [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['ayahs.0.ayah_number']);
    }

    public function test_index_returns_statuses_in_response(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized', 'bookmarked'],
        ]);

        $response = $this->getJson('/api/v1/customers/memorized', [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.0.statuses', ['memorized', 'bookmarked']);
    }

    public function test_index_filters_by_status_using_json_contains(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized', 'bookmarked'],
        ]);

        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 2,
            'memorized_at' => now(),
            'statuses' => ['weak'],
        ]);

        $response = $this->getJson('/api/v1/customers/memorized?status=bookmarked', [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ayah_number', 1)
            ->assertJsonPath('data.0.statuses', ['memorized', 'bookmarked']);
    }

    public function test_index_returns_all_when_no_status_filter(): void
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
            'surah_id' => 1,
            'ayah_number' => 2,
            'memorized_at' => now(),
            'statuses' => ['weak'],
        ]);

        $response = $this->getJson('/api/v1/customers/memorized', [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
