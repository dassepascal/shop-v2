<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, Session};
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Darryldecode\Cart\CartCollection;
use Mary\Traits\Toast;
use App\Models\Menu;

new class extends Component {
    use Toast;

    public int $CartItems = 0;
    public CartCollection $content;
    public float $total;
    public string $url;
    public Collection $menus;

    public function mount(Collection $menus): void
    {
        $this->menus = $menus;
        $this->CartItems = Cart::getTotalQuantity();
        $this->content = Cart::getContent();
        $this->total = Cart::getTotal();
        $this->url = request()->url();
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        Session::invalidate();
        Session::regenerateToken();
        $this->redirect('/');
    }

    public function cleanCart(): void
    {
        Cart::clear();
        $this->updateCartItems();
        $this->info(__('Cart cleaned.'), position: 'toast-bottom');
    }

    public function deleteItem($item): void
    {
        Cart::remove($item);
        $this->updateCartItems();
        $this->info(__('Item deleted.'), position: 'toast-bottom');
    }

    #[On('cart-updated')]
    public function updateCartItems()
    {
        $this->CartItems = Cart::getTotalQuantity();
        $this->content = Cart::getContent();
        $this->total = Cart::getTotal();
    }

    public function isBlogPage(): bool
    {
        return str_contains($this->url, '/blog') || str_contains($this->url, '/category');
    }
};
?>

@php
    // Style commun des liens de la barre : même hauteur, même forme, couleur héritée de la barre
    $navLink = 'btn btn-ghost btn-sm rounded-full font-semibold text-inherit '
        . ($this->isBlogPage() ? 'hover:bg-base-200' : 'hover:bg-white/15');
@endphp

<x-nav sticky full-width :class="App::isDownForMaintenance()
    ? 'bg-error text-error-content'
    : ($this->isBlogPage()
        ? 'bg-base-100 text-base-content border-b border-base-300 shadow-sm'
        : 'bg-linear-to-r from-cyan-600 to-cyan-700 text-white shadow-md')">
    <x-slot:brand>
        <label for="main-drawer" class="mr-3 lg:hidden">
            <x-icon name="o-bars-3" class="cursor-pointer" />
        </label>
        <a href="{{ $this->isBlogPage() ? route('blog.index') : '/' }}" wire:navigate>
            <x-app-brand />
        </a>
    </x-slot:brand>
    <x-slot:actions>
        <div class="hidden lg:flex items-center gap-1">
            @if ($this->isBlogPage())
                <a href="{{ route('blog.index') }}" wire:navigate class="{{ $navLink }}">{{ __('Articles') }}</a>
                <a href="{{ route('home') }}" wire:navigate class="{{ $navLink }}">{{ __('Shop') }}</a>
                <a href="{{ route('contact') }}" wire:navigate class="{{ $navLink }}">{{ __('Contact') }}</a>

                <!-- Menus dynamiques -->
                <x-dropdown>
                    <x-slot:trigger>
                        <span class="{{ $navLink }}">
                            {{ __('Categories') }}
                            <x-icon name="o-chevron-down" class="w-4 h-4" />
                        </span>
                    </x-slot:trigger>
                    <div class="text-base-content min-w-48">
                        @foreach ($menus as $menu)
                            @if ($menu->submenus->isNotEmpty())
                                <x-menu-sub title="{{ $menu->label }}">
                                    @foreach ($menu->submenus as $submenu)
                                        <x-menu-item title="{{ $submenu->label }}" link="{{ $submenu->link }}" />
                                    @endforeach
                                </x-menu-sub>
                            @else
                                <x-menu-item title="{{ $menu->label }}" link="{{ $menu->link }}"
                                    :external="Str::startsWith($menu->link, 'http')" />
                            @endif
                        @endforeach
                    </div>
                </x-dropdown>
            @else
                @if ($CartItems > 0 && $url !== route('cart') && $url !== route('order.index'))
                    <x-dropdown right>
                        <x-slot:trigger>
                            <span class="{{ $navLink }}">
                                <x-icon name="o-shopping-cart" class="w-5 h-5" />
                                {{ __('Cart') }}
                                <span class="badge badge-sm">{{ $CartItems }}</span>
                            </span>
                        </x-slot:trigger>
                        <div class="p-2 text-base-content {{ $content->isNotEmpty() ? 'min-w-[300px]' : '' }}">
                            @foreach ($content as $item)
                                <div class="flex justify-between mb-2">
                                    <div class="flex gap-4">
                                        <img class="object-cover w-14 h-14 rounded"
                                            src="{{ asset('storage/photos/' . $item->attributes->image) }}"
                                            alt="{{ $item->name }}" />
                                        <div class="mt-2">
                                            <span class="font-bold">{{ $item->name }}</span><br>
                                            {{ number_format($item->quantity * $item->price, 2, ',', ' ') }} €
                                            <br>@lang('Quantity:') {{ $item->quantity }}
                                        </div>
                                    </div>
                                    <x-button icon="o-trash" wire:click="deleteItem({{ $item->id }})"
                                        class="text-error btn-circle btn-ghost btn-sm" />
                                </div>
                                <hr class="border-base-300 mb-2">
                            @endforeach
                            <div class="flex justify-between items-center mt-3 mb-1">
                                <div class="font-bold">
                                    @if ($CartItems > 1)
                                        @lang('Total of my') {{ $CartItems }} @lang('articles')
                                    @else
                                        @lang('Total of my article')
                                    @endif
                                </div>
                                <div class="font-bold">{{ number_format($total, 2, ',', ' ') }} € TTC</div>
                            </div>
                            <p class="mb-4 text-right"><em>@lang('Excluding delivery')</em></p>
                            <hr class="border-base-300">
                            <div class="flex gap-2 justify-between items-center mt-4">
                                <x-button label="{{ __('Trash my cart') }}" wire:click="cleanCart"
                                    class="text-error btn-ghost btn-sm" />
                                <x-button label="{{ __('View my cart') }}" link="{{ route('cart') }}"
                                    icon-right="c-arrow-right" class="btn-primary btn-sm" />
                            </div>
                        </div>
                    </x-dropdown>
                @endif
                <a href="{{ route('blog.index') }}" wire:navigate class="{{ $navLink }}">{{ __('Blog') }}</a>
                <a href="{{ route('contact') }}" wire:navigate class="{{ $navLink }}">{{ __('Contact') }}</a>
            @endif

            @if ($user = auth()->user())
                <x-dropdown right>
                    <x-slot:trigger>
                        <span class="{{ $navLink }}">
                            <x-icon name="o-user-circle" class="w-5 h-5" />
                            {{ $user->name }} {{ $user->firstname }}
                            <x-icon name="o-chevron-down" class="w-4 h-4" />
                        </span>
                    </x-slot:trigger>
                    <div class="text-base-content min-w-48">
                        @if ($user->isAdmin())
                            <x-menu-item title="{{ __('Administration') }}" icon="o-cog-6-tooth" link="{{ route('admin.dashboard') }}" />
                        @endif
                        <x-menu-item title="{{ __('My profile') }}" icon="o-user" link="{{ route('profile') }}" />
                        <x-menu-item title="{{ __('My addresses') }}" icon="o-map-pin" link="{{ route('addresses') }}" />
                        <x-menu-item title="{{ __('My orders') }}" icon="o-shopping-bag" link="{{ route('orders') }}" />
                        <x-menu-item title="{{ __('RGPD') }}" icon="o-shield-check" link="{{ route('rgpd') }}" />
                        <x-menu-separator />
                        <x-menu-item title="{{ __('Logout') }}" icon="o-power" wire:click="logout" />
                    </div>
                </x-dropdown>
            @else
                <a href="/login" class="{{ $navLink }}">{{ __('Login') }}</a>
            @endif

            <x-theme-toggle title="{{ __('Toggle theme') }}" class="btn btn-ghost btn-sm btn-circle text-inherit" />

            @if ($this->isBlogPage())
                <livewire:search />
            @endif
        </div>
    </x-slot:actions>
</x-nav>
