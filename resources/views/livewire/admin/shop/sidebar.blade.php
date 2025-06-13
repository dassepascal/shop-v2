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
        <x-menu-item title="{{ __('Dashboard') }}" icon="s-building-storefront" link="{{ route('admin.dashboard') }}" />
        <x-menu-item title="{{ __('Dashboard shop') }}" icon="s-building-storefront" link="{{ route('admin.shop.dashboard') }}" />
        <x-menu-item title="{{ __('Orders') }}" icon="s-shopping-bag" link="{{ route('admin.shop.orders.index') }}" />
        <x-menu-item title="{{ __('Products') }}" icon="s-cube" link="{{ route('admin.shop.products.index') }}" />
        <x-menu-sub title="{{ __('Customers') }}" icon="s-users">
            <x-menu-item title="{{ __('Listing') }}" icon="s-list-bullet" link="{{ route('admin.shop.customers.index') }}"/>
        </x-menu-sub>
        <x-menu-item icon="s-building-storefront" title="{{ __('Catalogue') }}" link="{{ route('admin.shop.products.index') }}" />
        <x-menu-separator />
        <x-menu-sub title="{{ __('Settings') }}" icon="s-cog-8-tooth">
            <x-menu-item title="{{ __('Store') }}" icon="c-building-storefront" link="{{ route('admin.shop.parameters.store') }}" />
            <x-menu-item title="{{ __('Order status') }}" icon="m-eye" link="{{ route('admin.shop.parameters.states.index') }}" />
            <x-menu-item title="{{ __('Countries') }}" icon="c-map-pin" link="{{ route('admin.shop.parameters.countries.index') }}" />
            <x-menu-item title="{{ __('Pages') }}" icon="o-document-duplicate" link="{{ route('admin.shop.parameters.pages.index') }}" />
            <x-menu-sub title="{{ __('Shipments') }}" icon="s-truck">
                <x-menu-item title="{{ __('Ranges') }}" icon="o-circle-stack" link="{{ route('admin.shop.parameters.shipping.ranges') }}" />
                <x-menu-item title="{{ __('Rates') }}" icon="s-currency-euro" link="{{ route('admin.shop.parameters.shipping.rates') }}" />
            </x-menu-sub>
        </x-menu-sub>
        <x-menu-item title="{{ __('Statistics') }}" icon="s-chart-pie" link="{{ route('admin.shop.stats') }}" />
        <x-menu-item title="{{ __('Maintenance') }}" icon="c-wrench-screwdriver" link="{{ route('admin.shop.maintenance') }}" />
        <x-menu-separator />


        <x-menu-item icon="m-arrow-right-end-on-rectangle" title="{{ __('Go on store') }}" link="/" />
        <x-menu-item>
            <x-theme-toggle />
        </x-menu-item>
    </x-menu>
</div>
