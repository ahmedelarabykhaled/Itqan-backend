<?php

namespace Tests\Feature;

use App\Filament\Resources\Customers\Pages\ViewCustomer;
use App\Filament\Resources\Customers\RelationManagers\MemorizedAyahsRelationManager;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserMemorizedAyah;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerMemorizedHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');

        $this->admin = User::factory()->create();
        $this->actingAs($this->admin);

        $this->customer = Customer::create([
            'name' => 'Memorization Viewer',
            'email' => 'memorization-viewer-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);
    }

    public function test_customer_view_page_shows_memorization_summary(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now()->subDay(),
            'statuses' => ['memorized'],
        ]);

        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 2,
            'ayah_number' => 255,
            'memorized_at' => now(),
            'statuses' => ['memorized', 'bookmarked'],
        ]);

        $this->get('/admin?lang=ar')->assertOk();

        Livewire::test(ViewCustomer::class, ['record' => $this->customer->getRouteKey()])
            ->assertOk()
            ->assertSee(__('admin.customer.memorized_count'))
            ->assertSee('2')
            ->assertSee(__('admin.relations.memorized_ayahs'));
    }

    public function test_memorized_history_relation_manager_lists_all_records_for_the_customer(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 7,
            'memorized_at' => now(),
            'statuses' => ['memorized', 'saved'],
        ]);

        $otherCustomer = Customer::create([
            'name' => 'Other Customer',
            'email' => 'other-memorization-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'female',
        ]);

        UserMemorizedAyah::query()->create([
            'user_id' => $otherCustomer->id,
            'surah_id' => 114,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['bookmarked'],
        ]);

        $this->get('/admin?lang=ar')->assertOk();

        Livewire::test(MemorizedAyahsRelationManager::class, [
            'ownerRecord' => $this->customer,
            'pageClass' => ViewCustomer::class,
        ])
            ->assertOk()
            ->assertCanSeeTableRecords($this->customer->memorizedAyahs)
            ->assertCanNotSeeTableRecords($otherCustomer->memorizedAyahs)
            ->assertSee(__('admin.memorized_statuses.memorized'))
            ->assertSee(__('admin.memorized_statuses.saved'));
    }

    public function test_memorized_history_can_be_filtered_by_status(): void
    {
        $memorized = UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        $bookmarked = UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 1,
            'ayah_number' => 2,
            'memorized_at' => now(),
            'statuses' => ['bookmarked'],
        ]);

        Livewire::test(MemorizedAyahsRelationManager::class, [
            'ownerRecord' => $this->customer,
            'pageClass' => ViewCustomer::class,
        ])
            ->filterTable('status', 'bookmarked')
            ->assertCanSeeTableRecords([$bookmarked])
            ->assertCanNotSeeTableRecords([$memorized]);
    }

    public function test_customer_has_memorized_ayahs_relationship(): void
    {
        UserMemorizedAyah::query()->create([
            'user_id' => $this->customer->id,
            'surah_id' => 3,
            'ayah_number' => 10,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        $this->assertCount(1, $this->customer->memorizedAyahs);
        $this->assertTrue($this->customer->memorizedAyahs->first()->is(
            UserMemorizedAyah::query()->where('user_id', $this->customer->id)->first()
        ));
    }
}
