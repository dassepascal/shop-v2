<div>
    <x-menu activate-by-route>
        <x-menu-separator />
        <x-list-item :item="Auth::user()" value="name" sub-value="email" no-separator no-hover class="-mx-2 !-my-2 rounded">
            <x-slot:actions>
                <x-button icon="o-power" wire:click="logout" class="btn-circle btn-ghost btn-xs"
                    tooltip-left="{{ __('Logout') }}" no-wire-navigate />
            </x-slot:actions>
        </x-list-item>
   
        <x-menu-separator />
        <x-menu-item title="{{ __('Dashboard') }}" icon="s-building-storefront" link="{{ route('admin.shop.dashboard') }}" />
        <x-menu-item title="{{ __('Orders') }}" icon="s-shopping-bag" link="{{ route('admin.shop.orders.index') }}" />
        <x-menu-item title="{{ __('Products') }}" icon="s-cube" link="{{ route('admin.shop.products.index') }}" />
        <x-menu-item title="{{ __('Customers') }}" icon="s-users" link="{{ route('admin.shop.customers.index') }}" />
        <x-menu-separator />
        <x-menu-item title="{{ __('Statistics') }}" icon="s-chart-pie" link="{{ route('admin.shop.stats') }}" />
        <x-menu-item title="{{ __('Maintenance') }}" icon="c-wrench-screwdriver" link="{{ route('admin.shop.maintenance') }}" />
        <x-menu-separator />
        <x-menu-item title="{{ __('Settings') }}" icon="s-cog-8-tooth" link="{{ route('admin.shop.parameters.store') }}" />
    </x-menu>
</div>
