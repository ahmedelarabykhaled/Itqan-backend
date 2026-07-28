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

    public function test_bulk_store_creates_new_memorized_ayahs(): void
    {
        $payload = [
            'ayahs' => [
                ['surah_id' => 1, 'ayah_number' => 1],
                ['surah_id' => 1, 'ayah_number' => 2],
                ['surah_id' => 2, 'ayah_number' => 255],
            ],
        ];

        $response = $this->postJson('/api/v1/customers/memorized', $payload, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Ayahs memorized successfully')
            ->assertJsonCount(3, 'data');

        foreach ($payload['ayahs'] as $ayah) {
            $this->assertDatabaseHas('user_memorized_ayahs', [
                'user_id' => $this->customer->id,
                'surah_id' => $ayah['surah_id'],
                'ayah_number' => $ayah['ayah_number'],
            ]);
        }
    }

    public function test_bulk_store_with_single_ayah(): void
    {
        $payload = [
            'ayahs' => [
                ['surah_id' => 3, 'ayah_number' => 10],
            ],
        ];

        $response = $this->postJson('/api/v1/customers/memorized', $payload, [
            'Authorization' => 'Bearer '.$this->token,
        ]);

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.surah_id', 3)
            ->assertJsonPath('data.0.ayah_number', 10);
    }

    public function test_bulk_store_updates_memorized_at_for_existing_ayahs(): void
    {
        $oldTime = now()->subDays(5);

        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => $oldTime,
        ]);

        $payload = [
            'ayahs' => [
                ['surah_id' => 1, 'ayah_number' => 1],
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
}
