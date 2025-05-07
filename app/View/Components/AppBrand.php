<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppBrand extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return <<<'HTML'
            <a href="/" wire:navigate class="flex items-center">
                <!-- Hidden when collapsed -->
                <div {{ $attributes->class(['hidden-when-collapsed']) }}>
                    <img src="{{ asset('storage/photos/wp-logo.jpg') }}" alt="Logo" class="h-auto w-12 sm:w-14 md:w-16 lg:w-20 xl:w-24">
                </div>

                <!-- Display when collapsed -->
                <div class="display-when-collapsed hidden mx-5 mt-4 lg:mb-6 h-[28px]">
                    <x-icon name="s-square-3-stack-3d" class="w-6 text-purple-500 -mb-1" />
                </div>
            </a>
HTML;
    }
}
