<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <div class="container mx-auto mt-10">
        <h1 class="text-3xl font-bold mb-6">{{ __('Admin Dashboard') }}</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ route('admin.shop.dashboard') }}" class="block p-6 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">
                <h2 class="text-xl font-bold">{{ __('Shop Dashboard') }}</h2>
                <p>{{ __('Manage products, orders, and customers.') }}</p>
            </a>
            <a href="{{ route('admin.blog.dashboard') }}" class="block p-6 bg-green-500 text-white rounded-lg shadow hover:bg-green-600">
                <h2 class="text-xl font-bold">{{ __('Blog Dashboard') }}</h2>
                <p>{{ __('Manage posts, categories, and blog settings.') }}</p>
            </a>
        </div>
    </div>
   
</div>
