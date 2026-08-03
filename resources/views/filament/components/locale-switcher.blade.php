@php
    $currentLocale = app()->getLocale();

    $locales = [
        'ar' => 'العربية',
        'en' => 'English',
    ];
@endphp

<x-filament::dropdown placement="bottom-end" teleport>
    <x-slot name="trigger">
        <x-filament::icon-button
            color="gray"
            icon="heroicon-o-language"
            :label="__('admin.locale.switch')"
            :tooltip="__('admin.locale.switch')"
        />
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($locales as $code => $label)
            <x-filament::dropdown.list.item
                tag="a"
                :href="request()->fullUrlWithQuery(['lang' => $code])"
                :icon="$code === $currentLocale ? 'heroicon-m-check' : null"
                :color="$code === $currentLocale ? 'primary' : 'gray'"
            >
                {{ $label }}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
