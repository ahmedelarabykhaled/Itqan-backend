<?php

namespace App\Filament\Widgets;

use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

abstract class MonthlyTrendChartWidget extends ChartWidget
{
    protected const MONTHS = 12;

    protected const CACHE_TTL_MINUTES = 10;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '260px';

    abstract protected function getDatasetLabel(): string;

    abstract protected function getDateColumn(): string;

    abstract protected function newTrendQuery(): Builder;

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $trend = $this->getMonthlyTrend();

        return [
            'datasets' => [
                [
                    'label' => $this->getDatasetLabel(),
                    'data' => $trend->values()->all(),
                    'fill' => 'start',
                ],
            ],
            'labels' => $trend->keys()->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['precision' => 0],
                ],
            ],
        ];
    }

    /**
     * Counts keyed by a locale aware month label, oldest month first.
     *
     * @return Collection<string, int>
     */
    protected function getMonthlyTrend(): Collection
    {
        /** @var array<string, int> $counts */
        $counts = cache()->remember(
            'admin.monthly-trend.'.static::class,
            now()->addMinutes(static::CACHE_TTL_MINUTES),
            fn (): array => $this->countPerMonth(),
        );

        return collect($counts)->mapWithKeys(fn (int $count, string $month): array => [
            CarbonImmutable::createFromFormat('Y-m-d', $month.'-01')->translatedFormat('M Y') => $count,
        ]);
    }

    /**
     * @return array<string, int>
     */
    protected function countPerMonth(): array
    {
        $firstMonth = CarbonImmutable::now()->startOfMonth()->subMonths(static::MONTHS - 1);
        $column = $this->getDateColumn();

        $counts = [];

        for ($offset = 0; $offset < static::MONTHS; $offset++) {
            $month = $firstMonth->addMonths($offset);

            $counts[$month->format('Y-m')] = $this->newTrendQuery()
                ->whereBetween($column, [$month, $month->endOfMonth()])
                ->count();
        }

        return $counts;
    }
}
