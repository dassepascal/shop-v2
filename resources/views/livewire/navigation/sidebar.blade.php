<?php
use Illuminate\Support\Facades\{Auth, Session};
use Livewire\Volt\Component;

new class extends Component {
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
        {{-- Liens principaux --}}
        <x-menu-item title="{{ __('Shop') }}" icon="o-home" link="{{ route('home') }}" />
        <x-menu-item title="{{ __('Blog') }}" icon="o-newspaper" link="{{ route('blog.index') }}" />

        <x-menu-separator />

        {{-- Options spécifiques selon la page --}}
        @if ($this->isBlogPage())
            <x-menu-item title="{{ __('Articles') }}" icon="o-document-text" link="{{ route('blog.index') }}" />
            @auth
            <x-menu-item title="{{ __('Create a post') }}" icon="o-pencil" link="{{ route('posts.create') }}" />
            @endauth
            <x-menu-item title="{{ __('Search') }}" no-link no-hover class="my-2">
                <livewire:search />
            </x-menu-item>
        @else
            <x-menu-item title="{{ __('Contact') }}" icon="o-envelope" link="{{ route('contact') }}" />
        @endif

        <x-menu-separator />

        {{-- Utilisateur --}}
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
            <x-menu-item title="{{ __('Administration') }}" icon="o-cog" link="{{ route('admin') }}" />
            @endif
        @else
            <x-menu-item title="{{ __('Login') }}" icon="o-arrow-right-on-rectangle" link="/login" />
        @endif
    </x-menu>
</div>
