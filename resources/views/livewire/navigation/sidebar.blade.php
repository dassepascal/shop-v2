<?php

use Illuminate\Support\Facades\{Auth, Session};
use Livewire\Volt\Component;
use App\Models\Menu;

new class extends Component {
    public $menus;

    public function mount(): void
    {
        $this->menus = Menu::with(['submenus' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();
        $this->redirect('/');
    }

    public function isBlogPage(): bool
    {
        return str_contains(request()->url(), '/blog') || str_contains(request()->url(), '/category');
    }
};
?>

<div class="h-full p-4 bg-base-100">
    <x-menu activate-by-route class="flex flex-col space-y-2">
        <!-- Liens principaux -->
        <x-menu-item title="{{ __('Shop') }}" icon="o-home" link="{{ route('home') }}" />
        <x-menu-item title="{{ __('Blog') }}" icon="o-newspaper" link="{{ route('blog.index') }}" />
        <x-menu-item title="{{ __('Contact') }}" icon="o-envelope" link="{{ route('contact') }}" />

        <x-menu-separator />
        <!-- add contact -->


        @if ($this->isBlogPage())

        <x-menu-item title="{{ __('Contact') }}" icon="o-envelope" link="{{ route('contact') }}" />
        <!-- Menus dynamiques pour les catégories -->
        <x-dropdown label="Categories" class="btn-outline font-bold border flex items-center justify-center hover:text-gray-700 hover:bg-gray-100">
            @foreach ($menus as $menu)
            @if ($menu->submenus->isNotEmpty())
            <x-menu-sub title="{{ $menu->label }}" class="btn-ghost">
                @foreach ($menu->submenus as $submenu)
                <x-menu-item title="{{ $submenu->label }}" link="{{ $submenu->link }}" />
                @endforeach
            </x-menu-sub>
            @else
            <x-menu-item
                title="{{ $menu->label }}"
                link="{{ $menu->link }}"
                :external="Str::startsWith($menu->link, 'http')"
                class="text-base" />
            @endif
            @endforeach
        </x-dropdown>
        @else
        <x-menu-item title="{{ __('Contact') }}" icon="o-envelope" link="{{ route('contact') }}" />
        @endif

        <x-menu-separator />

        @if ($user = auth()->user())
        <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
            <x-slot:actions>
                <x-button icon="o-power" wire:click="logout" class="btn-circle btn-ghost btn-xs" tooltip-left="{{ __('Logout') }}" no-wire-navigate />
            </x-slot:actions>
        </x-list-item>
        <x-menu-item title="{{ __('My profile') }}" icon="o-user" link="{{ route('profile') }}" />
        <x-menu-item title="{{ __('My addresses') }}" icon="o-map-pin" link="{{ route('addresses') }}" />
        <x-menu-item title="{{ __('My orders') }}" icon="o-shopping-cart" link="{{ route('order.index') }}" />
        <x-menu-item title="{{ __('RGPD') }}" icon="o-lock-closed" link="{{ route('rgpd') }}" />
        @if ($user->isAdmin())
        <x-menu-separator />
        <x-dropdown label="{{ __('Administration') }}" icon="o-cog" class="btn-outline font-bold border flex items-center justify-center hover:text-gray-700 hover:bg-gray-100">
            <x-menu-item title="{{ __('Shop Dashboard') }}" link="{{ route('admin.shop.dashboard') }}" />
            <x-menu-item title="{{ __('Blog Dashboard') }}" link="{{ route('admin.blog.dashboard') }}" />
        </x-dropdown>
        @endif
        @else
        <x-menu-item title="{{ __('Login') }}" icon="o-arrow-right-on-rectangle" link="/login" />
        @endif
        </x-menu-item>
</div>
