<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\AdminAccountWidget;
use App\Filament\Widgets\AppStatsOverview;
use App\Filament\Widgets\ContentStatsOverview;
use App\Filament\Widgets\CustomersGrowthChart;
use App\Filament\Widgets\LatestCustomers;
use App\Filament\Widgets\MemorizationActivityChart;
use App\Filament\Widgets\MonthlyTrendChartWidget;
use App\Http\Middleware\SetPanelLocale;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserMemorizedAyah;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use ReflectionMethod;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        Filament::setCurrentPanel('admin');

        $this->actingAs($this->admin);
    }

    public function test_dashboard_page_renders_for_an_authenticated_admin(): void
    {
        $this->get('/admin')
            ->assertOk()
            ->assertSee(__('admin.dashboard.subheading'), escape: false);
    }

    public function test_dashboard_is_rendered_right_to_left_with_a_language_switcher(): void
    {
        $response = $this->get('/admin?lang=ar')->assertOk();

        $response->assertSee('dir="rtl"', escape: false);
        $response->assertSee(__('admin.locale.switch'), escape: false);
        $response->assertSee('English', escape: false);

        $this->get('/admin?lang=en')
            ->assertOk()
            ->assertSee('dir="ltr"', escape: false);
    }

    public function test_audience_stats_widget_reports_customer_metrics(): void
    {
        Customer::create([
            'name' => 'Verified Customer',
            'email' => 'verified-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        Customer::create([
            'name' => 'Unverified Customer',
            'email' => 'unverified-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'female',
        ]);

        Livewire::test(AppStatsOverview::class)
            ->assertOk()
            ->assertSee(__('admin.audience.heading'))
            ->assertSee(__('admin.audience.total'))
            ->assertSee(__('admin.audience.verified_description', ['percentage' => '50']));
    }

    public function test_content_stats_widget_renders(): void
    {
        Livewire::test(ContentStatsOverview::class)
            ->assertOk()
            ->assertSee(__('admin.content.heading'))
            ->assertSee(__('admin.content.mushaf_images'))
            ->assertSee(__('admin.content.tafaseer'))
            ->assertSee(__('admin.content.recitations'));
    }

    public function test_memorization_chart_buckets_the_last_twelve_months(): void
    {
        $customer = Customer::create([
            'name' => 'Memorizer',
            'email' => 'memorizer-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
            'email_verified_at' => now(),
        ]);

        UserMemorizedAyah::query()->create([
            'user_id' => $customer->id,
            'surah_id' => 1,
            'ayah_number' => 1,
            'memorized_at' => now(),
            'statuses' => ['memorized'],
        ]);

        $data = $this->chartDataOf(new MemorizationActivityChart);

        $this->assertCount(12, $data['labels']);
        $this->assertSame(1, end($data['datasets'][0]['data']));
    }

    public function test_customers_growth_chart_buckets_the_last_twelve_months(): void
    {
        Customer::create([
            'name' => 'Recent Customer',
            'email' => 'recent-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
        ]);

        $data = $this->chartDataOf(new CustomersGrowthChart);

        $this->assertCount(12, $data['labels']);
        $this->assertSame(1, end($data['datasets'][0]['data']));
    }

    public function test_latest_customers_widget_lists_the_most_recent_sign_ups(): void
    {
        $customer = Customer::create([
            'name' => 'Newest Customer',
            'email' => 'newest-'.uniqid().'@example.com',
            'password' => 'password',
            'gender' => 'male',
        ]);

        Livewire::test(LatestCustomers::class)
            ->assertOk()
            ->assertSee(__('admin.latest_customers.heading'))
            ->assertSee($customer->name)
            ->assertSee(__('admin.latest_customers.unverified'));
    }

    public function test_panel_defaults_to_the_configured_locale(): void
    {
        config(['app.panel_locale' => 'ar']);

        $this->get('/admin')->assertOk();

        $this->assertSame('ar', app()->getLocale());
    }

    public function test_panel_locale_can_be_switched_and_is_remembered(): void
    {
        $this->get('/admin?lang=en')->assertOk();

        $this->assertSame('en', app()->getLocale());
        $this->assertSame('en', session(SetPanelLocale::SESSION_KEY));

        $this->get('/admin')->assertOk();

        $this->assertSame('en', app()->getLocale());
    }

    public function test_panel_ignores_an_unsupported_locale(): void
    {
        config(['app.panel_locale' => 'ar']);

        $this->get('/admin?lang=fr')->assertOk();

        $this->assertSame('ar', app()->getLocale());
        $this->assertNull(session(SetPanelLocale::SESSION_KEY));
    }

    public function test_dashboard_registers_every_custom_widget(): void
    {
        $widgets = array_map(
            fn (mixed $widget): string => is_string($widget) ? $widget : $widget::class,
            (new Dashboard)->getWidgets(),
        );

        foreach ([
            AdminAccountWidget::class,
            AppStatsOverview::class,
            ContentStatsOverview::class,
            MemorizationActivityChart::class,
            CustomersGrowthChart::class,
            LatestCustomers::class,
        ] as $widget) {
            $this->assertContains($widget, $widgets);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function chartDataOf(MonthlyTrendChartWidget $widget): array
    {
        return (new ReflectionMethod($widget, 'getData'))->invoke($widget);
    }
}
