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
    public $menus;

    public function mount(): void
    {
        $this->menus = Menu::with(['submenus' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();
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

<x-nav sticky full-width :class="App::isDownForMaintenance() ? 'bg-red-300' : ($this->isBlogPage() ? 'bg-white shadow-sm text-black' : 'bg-cyan-700 text-white')">
    <x-slot:brand>
        <label for="main-drawer" class="mr-3 lg:hidden">
            <x-icon name="o-bars-3" class="cursor-pointer" />
        </label>
        <a href="{{ $this->isBlogPage() ? route('blog.index') : '/' }}" wire:navigate>
            <x-app-brand />
        </a>
    </x-slot:brand>
    <x-slot:actions>
        <span class="hidden lg:block">
            @if ($this->isBlogPage())
            <div class="flex items-center space-x-4">
                <!-- Liens statiques -->
                <x-menu>
                    <x-menu-item title="{{ __('Articles') }}" link="{{ route('blog.index') }}"
                        class="btn-outline font-bold border h-12 flex items-center justify-center hover:text-gray-700 hover:bg-gray-100" />
                </x-menu>
                <x-menu>
                    <x-menu-item title="{{ __('Shop') }}" link="{{ route('home') }}"
                        class="btn-outline font-bold border h-12 flex items-center justify-center hover:text-gray-700 hover:bg-gray-100" />
                </x-menu>
                <x-menu>
                    <x-menu-item title="{{ __('Contact') }}" link="{{ route('contact') }}"
                        class="btn-outline font-bold border h-12 flex items-center justify-center hover:text-gray-700 hover:bg-gray-100" />
                </x-menu>

                <!-- Menus dynamiques -->

                <x-dropdown label="Categories" class=" btn-outline font-bold border  flex items-center justify-center hover:text-gray-700 hover:bg-gray-100 " >
                @foreach ($menus as $menu)
                @if ($menu->submenus->isNotEmpty())
                    <x-menu-sub title="{{ $menu->label }}" class="btn-ghost"  >
                    @foreach ($menu->submenus as $submenu)
                        <x-menu-item title="{{ $submenu->label }}" link="{{ $submenu->link }}" style="min-width: max-content;" />
                    @endforeach

                    </x-menu-sub>
                    @else
                <x-button label="{{ $menu->label }}" link="{{ $menu->link }}" :external="Str::startsWith($menu->link, 'http')"
                    class="btn-ghost" />
            @endif
        @endforeach
                </x-dropdown>


                @if ($user = auth()->user())
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button label="{{ $user->name }} {{ $user->firstname }}"
                            class="btn-ghost h-10 flex items-center justify-center" />
                    </x-slot:trigger>
                    <span class="text-black">
                        @if ($user->isAdmin())
                        <x-menu-item title="{{ __('Administration') }}" link="{{ route('admin') }}" />
                        @endif
                        <x-menu-item title="{{ __('My profile') }}" link="{{ route('profile') }}" />
                        <x-menu-item title="{{ __('My addresses') }}" link="{{ route('addresses') }}" />
                        <x-menu-item title="{{ __('My orders') }}" link="{{ route('orders') }}" />
                        <x-menu-item title="{{ __('RGPD') }}" link="{{ route('rgpd') }}" />
                        <x-menu-item title="{{ __('Logout') }}" wire:click="logout" />
                    </span>
                </x-dropdown>
                @else
                <x-button label="{{ __('Login') }}" link="/login"
                    class="btn-ghost h-10 flex items-center justify-center" />
                @endif
                <x-theme-toggle title="{{ __('Toggle theme') }}" class="w-4 h-8" />
                <livewire:search />
            </div>
            @else
            @if ($CartItems > 0 && $url !== route('cart') && $url !== route('order.index'))
            <x-dropdown>
                <x-slot:trigger>
                    <x-button label="{{ __('Cart') }}" icon="o-shopping-cart" badge="{{ $CartItems }}"
                        badge-classes="badge-ghost" class="btn-ghost" />
                </x-slot:trigger>
                <div class="p-2 text-black {{ $content->isNotEmpty() ? 'min-w-[300px]' : '' }}">
                    @foreach ($content as $item)
                    <div class="flex justify-between mb-2">
                        <div class="flex gap-4">
                            <img class="object-cover w-14 h-14"
                                src="{{ asset('storage/photos/' . $item->attributes->image) }}"
                                alt="{{ $item->name }}" />
                            <div class="mt-2">
                                <span class="font-bold">{{ $item->name }}</span><br>
                                {{ number_format($item->quantity * $item->price, 2, ',', ' ') }} €
                                <br>@lang('Quantity:') {{ $item->quantity }}
                            </div>
                        </div>
                        <x-button icon="o-trash" wire:click="deleteItem({{ $item->id }})"
                            class="text-red-500 btn-circle btn-ghost btn-sm" />
                    </div>
                    <hr><br>
                    @endforeach
                    <br>
                    <div class="flex justify-between items-center mb-1">
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
                    <hr>
                    <div class="flex gap-2 justify-between items-center mt-4">
                        <x-button label="{{ __('Trash my cart') }}" wire:click="cleanCart"
                            class="text-red-500 btn-ghost btn-sm" />
                        <x-button label="{{ __('View my cart') }}" link="{{ route('cart') }}"
                            icon-right="c-arrow-right" class="btn-primary btn-sm" />
                    </div>
                </div>
            </x-dropdown>
            @endif
            <div class="flex items-center space-x-4 justify-start">
                <x-menu>
                    <x-menu-item title="{{ __('Blog') }}" link="{{ route('blog.index') }}"
                        class="btn-outline font-bold border h-10 flex items-center justify-center hover:text-white hover:bg-gray-300" />
                </x-menu>
                <x-menu>
                    <x-menu-item title="{{ __('Contact') }}" link="{{ route('contact') }}"
                        class="btn-outline font-bold border h-10 flex items-center justify-center hover:text-gray-700 hover:bg-gray-100" />
                </x-menu>
                @if ($user = auth()->user())
                <x-dropdown>
                    <x-slot:trigger>
                        <x-button label="{{ $user->name }} {{ $user->firstname }}"
                            class="btn-ghost h-10 flex items-center justify-center" />
                    </x-slot:trigger>
                    <span class="text-black">
                        @if ($user->isAdmin())
                        <x-menu-item title="{{ __('Administration') }}" link="{{ route('admin') }}" />
                        @endif
                        <x-menu-item title="{{ __('My profile') }}" link="{{ route('profile') }}" />
                        <x-menu-item title="{{ __('My addresses') }}" link="{{ route('addresses') }}" />
                        <x-menu-item title="{{ __('My orders') }}" link="{{ route('orders') }}" />
                        <x-menu-item title="{{ __('RGPD') }}" link="{{ route('rgpd') }}" />
                        <x-menu-item title="{{ __('Logout') }}" wire:click="logout" />
                    </span>
                </x-dropdown>
                @else
                <x-button label="{{ __('Login') }}" link="/login"
                    class="btn-ghost h-10 flex items-center justify-center" />
                @endif
                <x-theme-toggle title="{{ __('Toggle theme') }}" class="w-4 h-8" />
            </div>
            @endif

        </span>
    </x-slot:actions>
</x-nav>
