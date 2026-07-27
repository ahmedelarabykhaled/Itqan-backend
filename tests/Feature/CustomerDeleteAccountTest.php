<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\UserMemorizedAyah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerDeleteAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_delete_account_successfully(): void
    {
        Storage::fake('public');

        $customer = Customer::create([
            'name' => 'Delete Account User',
            'email' => 'delete-account-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
            'avatar' => 'customers/avatars/1/avatar.jpg',
        ]);

        $token = $customer->createToken('auth-token')->plainTextToken;

        DB::table('password_reset_tokens')->insert([
            'email' => $customer->email,
            'token' => Hash::make('reset-token'),
            'created_at' => now(),
        ]);

        UserMemorizedAyah::query()->create([
            'user_id' => $customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
        ]);

        Storage::disk('public')->put(
            'customers/avatars/'.$customer->id.'/avatar.jpg',
            'avatar-content'
        );

        $response = $this->deleteJson('/api/v1/customers/profile', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('customers.customer_deleted_successfully'));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => Customer::class,
            'tokenable_id' => $customer->id,
        ]);
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $customer->email,
        ]);
        $this->assertDatabaseMissing('user_memorized_ayahs', [
            'user_id' => $customer->id,
        ]);
        Storage::disk('public')->assertMissing('customers/avatars/'.$customer->id.'/avatar.jpg');
    }

    public function test_delete_account_fails_when_unauthenticated(): void
    {
        $response = $this->deleteJson('/api/v1/customers/profile');

        $response->assertUnauthorized();
    }
}
