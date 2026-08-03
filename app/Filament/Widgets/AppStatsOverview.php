<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\UserMemorizedAyah;
use Carbon\CarbonImmutable;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class AppStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = null;

    protected function getHeading(): ?string
    {
        return __('admin.audience.heading');
    }

    protected function getDescription(): ?string
    {
        return __('admin.audience.description');
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $totalCustomers = Customer::query()->count();
        $verifiedCustomers = Customer::query()->verified()->count();
        $memorizers = UserMemorizedAyah::query()->distinct()->count('user_id');

        $now = CarbonImmutable::now();
        $newThisWeek = Customer::query()->where('created_at', '>=', $now->subWeek())->count();
        $newLastWeek = Customer::query()
            ->whereBetween('created_at', [$now->subWeeks(2), $now->subWeek()])
            ->count();

        return [
            Stat::make(__('admin.audience.total'), Number::format($totalCustomers))
                ->description(__('admin.audience.total_description'))
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->chart($this->getDailySignups())
                ->color('primary'),

            Stat::make(__('admin.audience.verified'), Number::format($verifiedCustomers))
                ->description(__('admin.audience.verified_description', [
                    'percentage' => $this->asPercentage($verifiedCustomers, $totalCustomers),
                ]))
                ->descriptionIcon(Heroicon::OutlinedCheckBadge)
                ->color('success'),

            Stat::make(__('admin.audience.new_this_week'), Number::format($newThisWeek))
                ->description(__('admin.audience.new_this_week_description', [
                    'previous' => Number::format($newLastWeek),
                ]))
                ->descriptionIcon($newThisWeek >= $newLastWeek
                    ? Heroicon::OutlinedArrowTrendingUp
                    : Heroicon::OutlinedArrowTrendingDown)
                ->color($newThisWeek >= $newLastWeek ? 'success' : 'danger'),

            Stat::make(__('admin.audience.memorizers'), Number::format($memorizers))
                ->description(__('admin.audience.memorizers_description', [
                    'percentage' => $this->asPercentage($memorizers, $totalCustomers),
                ]))
                ->descriptionIcon(Heroicon::OutlinedBookOpen)
                ->color('info'),
        ];
    }

    /**
     * Sign-up counts for the last seven days, oldest day first.
     *
     * @return array<int, int>
     */
    protected function getDailySignups(): array
    {
        return cache()->remember('admin.audience.daily-signups', now()->addMinutes(10), function (): array {
            $today = CarbonImmutable::now()->startOfDay();

            return collect(range(6, 0))
                ->map(function (int $daysAgo) use ($today): int {
                    $day = $today->subDays($daysAgo);

                    return Customer::query()
                        ->whereBetween('created_at', [$day, $day->endOfDay()])
                        ->count();
                })
                ->all();
        });
    }

    protected function asPercentage(int $value, int $total): string
    {
        if ($total === 0) {
            return '0';
        }

        return (string) round(($value / $total) * 100);
    }
}
