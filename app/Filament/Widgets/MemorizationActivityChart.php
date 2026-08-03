<?php

namespace App\Filament\Widgets;

use App\Models\UserMemorizedAyah;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class MemorizationActivityChart extends MonthlyTrendChartWidget
{
    protected static ?int $sort = 3;

    protected string $color = 'success';

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.charts.memorization.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('admin.charts.memorization.description');
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getDatasetLabel(): string
    {
        return __('admin.charts.memorization.dataset');
    }

    protected function getDateColumn(): string
    {
        return 'memorized_at';
    }

    protected function newTrendQuery(): Builder
    {
        return UserMemorizedAyah::query();
    }
}
