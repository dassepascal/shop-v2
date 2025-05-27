<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="min-h-[35vw] hero" style="background-image: url({{asset('storage/hero.jpg')}})">
        <div class="bg-opacity-60 hero-overlay"></div>
        <a href="{{ route('blog.index') }}">
            <div class="text-center hero-content text-neutral-content">
                <div>
                    <h1 class="mb-5 text-4xl font-bold sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl">
                    {{ config('app.title') }}
                    <p class="mb-5 text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl">
                        {{ config('app.subTitle') }}
                    </p>
                </div>
            </div>
        </a>



    </div>
