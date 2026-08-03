<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Http\Middleware\SetPanelLocale;
use Filament\Forms\Components\Field;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Components\Entry;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        $this->registerAttributeLabels();
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.components.locale-switcher')->render(),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetPanelLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Filament derives a component's label from its attribute name, which always
     * reads as English. Resolving those names through the `admin.attributes`
     * translations keeps every table, form, infolist and filter in the panel's
     * active language. Components that set their own label still win, and names
     * without a translation fall back to Filament's generated label.
     */
    protected function registerAttributeLabels(): void
    {
        $useTranslatedLabel = function (Column|Field|Entry|BaseFilter $component): void {
            $name = $component->getName();

            $component->label(fn (): ?string => static::translateAttribute($name));
        };

        Column::configureUsing($useTranslatedLabel);
        Field::configureUsing($useTranslatedLabel);
        Entry::configureUsing($useTranslatedLabel);
        BaseFilter::configureUsing($useTranslatedLabel);
    }

    protected static function translateAttribute(string $name): ?string
    {
        foreach ([$name, Str::afterLast($name, '.')] as $attribute) {
            if (Lang::has($key = "admin.attributes.{$attribute}")) {
                return __($key);
            }
        }

        return null;
    }
}
