<?php

namespace Obelaw\Configs\Filament;

use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Obelaw\Configs\Filament\Pages\ConfigsPage;
use Obelaw\Twist\Base\BaseAddon;

class ConfigPlugin extends BaseAddon
{
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
