<?php

use App\Traits\ManageOrders;
use Livewire\Volt\Component;
use App\Services\OrderService;
use App\Models\{Order, Product, User, Setting};
use Barryvdh\Debugbar\Facades\Debugbar;
use Livewire\Attributes\{Layout, Title};

new #[Title('Blog management')] #[Layout('components.layouts.admin')] class extends Component
{

}
?>

<div>
    <x-header title="{!! __('Blog management') !!}" separator progress-indicator>
        <x-slot:actions>
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline lg:hidden"
                link="{{ route('admin.shop.dashboard') }}" />
        </x-slot:actions>
    </x-header>
</div>
