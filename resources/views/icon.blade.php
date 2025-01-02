<x-filament::link href="{{ route('filament.' . filament()->getCurrentPanel()->getId() . '.pages.configs-page') }}">
    <x-filament::icon alias="panels::topbar.global-search.field" icon="heroicon-m-cog-6-tooth" wire:target="search"
        class="h-5 w-5 text-gray-500 dark:text-gray-400" />
</x-filament::link>
