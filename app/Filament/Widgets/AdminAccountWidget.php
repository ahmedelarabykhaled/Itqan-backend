<?php

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget;

/**
 * Filament's account widget, widened so it does not leave a gap next to it
 * on the two column dashboard grid.
 */
class AdminAccountWidget extends AccountWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';
}
