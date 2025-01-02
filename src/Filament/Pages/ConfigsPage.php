<?php

namespace Obelaw\Configs\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Obelaw\Configs\Schema\Section as SchemaSection;

/**
 * BaseSettingsPage
 */
class ConfigsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'obelaw-configs::configs';

    public $inputs = [];

    /**
     * The header for save action.
     *
     * @return array
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Save')
                ->icon('heroicon-o-check')
                ->action(fn(): mixed => $this->save($this->validate())),
        ];
    }

    public function mount(): void
    {
        $filled = [];

        foreach (SchemaSection::getSectionsKeyValueConfigs() as $key => $value) {
            $filled['inputs.' . $key] = o_config()->get($value);
        }

        $this->form->fill($filled);
    }

    protected function getFormSchema(): array
    {
        $sections = SchemaSection::getSections();

        $render = [];

        foreach ($sections as $section) {
            $render[] = Section::make($section['name'])
                ->icon('heroicon-m-shopping-bag')
                ->description($section['description'])
                ->schema(array_map(function ($input) {
                    return TextInput::make('inputs.' . $input['name'])
                        ->label($input['label'])
                        ->required();
                }, $section['schema']))
                ->collapsible()
                ->persistCollapsed();
        }

        return $render;
    }

    public function save($inputs)
    {
        $congigs = SchemaSection::getSectionsKeyValueConfigs();

        foreach ($inputs['inputs'] as $input => $value) {
            o_config()->set($congigs[$input], $value);
        }
    }
};
