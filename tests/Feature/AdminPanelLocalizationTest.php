<?php

namespace Tests\Feature;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\QuranTranslations\Pages\ListQuranTranslations;
use App\Models\Customer;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');

        $this->actingAs(User::factory()->create());
    }

    public function test_table_columns_use_the_arabic_attribute_labels(): void
    {
        Customer::create([
            'name' => 'عميل تجريبي',
            'email' => 'localised-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
        ]);

        $this->get('/admin?lang=ar')->assertOk();

        Livewire::test(ListCustomers::class)
            ->assertOk()
            ->assertSee(__('admin.attributes.email'))
            ->assertSee(__('admin.attributes.gender'))
            ->assertSee(__('admin.attributes.provider_id'));
    }

    public function test_table_columns_switch_back_to_english_labels(): void
    {
        $this->get('/admin?lang=en')->assertOk();

        Livewire::test(ListCustomers::class)
            ->assertOk()
            ->assertSee('Email address')
            ->assertSee('Provider ID');
    }

    public function test_quran_translation_columns_are_translated(): void
    {
        $this->get('/admin?lang=ar')->assertOk();

        Livewire::test(ListQuranTranslations::class)
            ->assertOk()
            ->assertSee(__('admin.attributes.display_name'))
            ->assertSee(__('admin.attributes.language_code'))
            ->assertSee(__('admin.attributes.current_version'));
    }

    public function test_table_filters_are_translated(): void
    {
        app()->setLocale('ar');

        $this->assertSame(
            __('admin.attributes.language_code'),
            SelectFilter::make('language_code')->getLabel(),
        );
    }

    public function test_resource_labels_follow_the_active_locale(): void
    {
        app()->setLocale('ar');
        $this->assertSame(__('admin.resources.customer.plural_label'), CustomerResource::getPluralModelLabel());

        app()->setLocale('en');
        $this->assertSame('App users', CustomerResource::getPluralModelLabel());
    }

    public function test_a_component_keeps_its_own_label_over_the_translated_one(): void
    {
        app()->setLocale('ar');

        $column = TextColumn::make('email')->label('Custom label');

        $this->assertSame('Custom label', $column->getLabel());
    }

    public function test_an_attribute_without_a_translation_falls_back_to_the_generated_label(): void
    {
        app()->setLocale('ar');

        $column = TextColumn::make('some_unmapped_attribute');

        $this->assertSame('Some unmapped attribute', $column->getLabel());
    }

    public function test_gender_values_are_rendered_in_arabic(): void
    {
        Customer::create([
            'name' => 'Female Customer',
            'email' => 'female-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'female',
        ]);

        $this->get('/admin?lang=ar')->assertOk();

        Livewire::test(ListCustomers::class)
            ->assertOk()
            ->assertSee(__('admin.gender.female'));
    }
}
