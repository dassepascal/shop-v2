<?php
use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {
    public Collection $menus;

    public function mount(Collection $menus): void
    {
        $this->menus = $menus;
    }
}; ?>




<x-nav sticky full-width>
    <x-slot:brand>
        <label for="main-drawer" class="mr-3 lg:hidden">
            <x-icon name="o-bars-3" class="cursor-pointer" />
        </label>
    </x-slot:brand>

    <x-slot:actions>
        <span class="hidden lg:block">

            @if ($user = auth()->user())
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button label="{{ $user->name }}" class="btn-ghost" />
                    </x-slot:trigger>
                    <x-menu-item title="{{ __('Profile') }}" link="{{ route('profile') }}" />
                    <x-menu-item title="{{ __('Logout') }}" wire:click="logout" />
                     @if ($user->isAdminOrRedac())
                    <x-menu-item title="{{ __('Administration') }}" link="{{ route('admin') }}" />
                @endif
                </x-dropdown>
            @else
            <div class="flex items-center space-x-4">
            @foreach ($menus as $menu)
            @if ($menu->submenus->isNotEmpty())
                <x-dropdown >
                    <x-slot:trigger >
                        <x-button label="{{ $menu->label }}" class="btn-ghost" />
                    </x-slot:trigger>
                    @foreach ($menu->submenus as $submenu)
                        <x-menu-item title="{{ $submenu->label }}" link="{{ $submenu->link }}"
                            style="min-width: max-content;" />
                    @endforeach
                </x-dropdown>
            @else
                <x-button label="{{ $menu->label }}" link="{{ $menu->link }}" :external="Str::startsWith($menu->link, 'http')"
                    class="btn-ghost" />
            @endif
        @endforeach
                <x-menu>
                    <x-menu-item title="Shop" link="{{ route('home') }}"
                        class="btn-outline font-bold border h-10 flex items-center justify-center hover:text-white hover:bg-gray-300" />
                </x-menu>
                <x-menu>
                    <x-menu-item title="{{ __('Login') }}" link="/login"
                        class="btn-outline font-bold border h-10 flex items-center justify-center hover:text-white hover:bg-gray-300" />
                </x-menu>
            </div>

            @endif

        </span>


       
    </x-slot:actions>
</x-nav>
