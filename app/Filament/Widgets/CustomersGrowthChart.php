<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class CustomersGrowthChart extends MonthlyTrendChartWidget
{
    protected static ?int $sort = 4;

    protected string $color = 'primary';

    public function getHeading(): string|Htmlable|null
    {
        return __('admin.charts.customers.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('admin.charts.customers.description');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getDatasetLabel(): string
    {
        return __('admin.charts.customers.dataset');
    }

    protected function getDateColumn(): string
    {
        return 'created_at';
    }

    protected function newTrendQuery(): Builder
    {
        return Customer::query();
    }
}
