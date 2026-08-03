<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Customer;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;

class LatestCustomers extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(Customer::query()->latest())
            ->recordUrl(fn (Customer $record): string => CustomerResource::getUrl('view', ['record' => $record]))
            ->defaultPaginationPageOption(5)
            ->paginated([5, 10, 25])
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (Customer $record): string => Filament::getUserAvatarUrl($record)),

                TextColumn::make('name')
                    ->label(__('admin.latest_customers.name'))
                    ->weight('medium')
                    ->description(fn (Customer $record): string => $record->email)
                    ->searchable(['name', 'email']),

                TextColumn::make('provider')
                    ->label(__('admin.latest_customers.provider'))
                    ->badge()
                    ->placeholder(__('admin.latest_customers.provider_email')),

                TextColumn::make('status')
                    ->label(__('admin.latest_customers.status'))
                    ->badge()
                    ->getStateUsing(fn (Customer $record): string => $record->email_verified_at !== null
                        ? __('admin.latest_customers.verified')
                        : __('admin.latest_customers.unverified'))
                    ->color(fn (Customer $record): string => $record->email_verified_at !== null ? 'success' : 'warning'),

                TextColumn::make('created_at')
                    ->label(__('admin.latest_customers.registered_at'))
                    ->since()
                    ->sortable(),
            ]);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('admin.latest_customers.heading');
    }
}
