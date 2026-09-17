<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerUpdateProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_customer_can_update_avatar_via_post(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $token = $customer->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/v1/customers/auth/update', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', __('customers.customer_updated_successfully'));

        $customer->refresh();

        $avatarPath = $customer->getRawOriginal('avatar');

        $this->assertNotNull($avatarPath);
        $this->assertStringStartsWith('customers/avatars/'.$customer->id.'/', $avatarPath);
        Storage::disk('public')->assertExists($avatarPath);
        $response->assertJsonPath('data.avatar', url('storage/'.$avatarPath));
    }

    public function test_customer_can_update_profile_fields_and_replace_avatar(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer([
            'avatar' => 'customers/avatars/placeholder/old.jpg',
        ]);
        Storage::disk('public')->put('customers/avatars/placeholder/old.jpg', 'old-avatar');
        $originalPassword = $customer->getRawOriginal('password');
        $token = $customer->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/v1/customers/auth/update', [
            'name' => 'Updated Name',
            'gender' => 'female',
            'avatar' => UploadedFile::fake()->image('new-avatar.png'),
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.gender', 'female');

        $customer->refresh();

        $this->assertSame('Updated Name', $customer->name);
        $this->assertSame('female', $customer->gender);
        $this->assertSame($originalPassword, $customer->getRawOriginal('password'));
        Storage::disk('public')->assertMissing('customers/avatars/placeholder/old.jpg');
        Storage::disk('public')->assertExists($customer->getRawOriginal('avatar'));
    }

    public function test_customer_can_update_name_via_put_without_changing_avatar(): void
    {
        $customer = $this->makeCustomer([
            'avatar' => 'customers/avatars/1/avatar.jpg',
        ]);
        $token = $customer->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->putJson('/api/v1/customers/auth/update', [
            'name' => 'Put Updated Name',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Put Updated Name');

        $customer->refresh();

        $this->assertSame('Put Updated Name', $customer->name);
        $this->assertSame('customers/avatars/1/avatar.jpg', $customer->getRawOriginal('avatar'));
    }

    public function test_update_fails_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/v1/customers/auth/update', [
            'name' => 'No Auth',
        ]);

        $response->assertUnauthorized();
    }

    public function test_update_fails_when_avatar_is_not_an_image(): void
    {
        Storage::fake('public');

        $customer = $this->makeCustomer();
        $token = $customer->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json',
        ])->post('/api/v1/customers/auth/update', [
            'avatar' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ]);

        $response->assertUnprocessable()
            ->assertJsonPath('success', false);

        $customer->refresh();
        $this->assertNull($customer->getRawOriginal('avatar'));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeCustomer(array $overrides = []): Customer
    {
        return Customer::query()->create(array_merge([
            'name' => 'Update Profile User',
            'email' => 'update-profile-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ], $overrides));
    }
}
