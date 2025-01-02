<?php

namespace Obelaw\Configs\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Obelaw\Configs\Filament\Pages\ConfigsPage;

class ConfigPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'obelaw-configs';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): View => view('obelaw-configs::icon'),
            )
            ->pages([ConfigsPage::class]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
