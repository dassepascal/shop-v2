
<div>
<!-- Menus dynamiques -->
@foreach ($menus as $menu)
@if ($menu->submenus->isNotEmpty())
    <x-dropdown>
        <x-slot:trigger>
            <x-button label="{{ $menu->label }}" class="btn-ghost h-10 flex items-center justify-center" />
        </x-slot:trigger>
        @foreach ($menu->submenus as $submenu)
            <x-menu-item title="{{ $submenu->label }}" link="{{ $submenu->link }}"
                style="min-width: max-content;" />
        @endforeach
    </x-dropdown>
@else
    <x-button label="{{ $menu->label }}" link="{{ $menu->link ?? '#' }}"
        :external="Str::startsWith($menu->link ?? '', 'http')"
        class="btn-ghost h-10 flex items-center justify-center" />
@endif
@endforeach

</div>
