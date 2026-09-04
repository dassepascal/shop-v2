<?php

use App\Models\Footer;
use Livewire\Volt\Component;

new class() extends Component {

};
?>

<footer class="text-white bg-gradient-to-b from-cyan-700 to-cyan-800">
    <div class="grid grid-cols-1 gap-10 p-10 mx-auto max-w-7xl sm:grid-cols-2 lg:grid-cols-4 footer">
        <nav class="flex flex-col gap-2">
            <h6 class="mb-2 text-sm font-semibold tracking-wide text-cyan-100 uppercase">@lang('Information')</h6>
            <a href="{{ route('pages', ['page' => 'livraisons']) }}"
                class="transition-colors duration-200 link link-hover hover:text-cyan-200">@lang('Shipping')</a>
            <a href="{{ route('pages', ['page' => 'mentions-legales']) }}"
                class="transition-colors duration-200 link link-hover hover:text-cyan-200">@lang('Legal informations')</a>
            <a href="{{ route('pages', ['page' => 'conditions-generales-de-vente']) }}"
                class="transition-colors duration-200 link link-hover hover:text-cyan-200">@lang('Terms and conditions of sale')</a>
        </nav>
        <nav class="flex flex-col gap-2">
            <h6 class="mb-2 text-sm font-semibold tracking-wide text-cyan-100 uppercase">@lang('Legal')</h6>
            <a href="{{ route('pages', ['page' => 'politique-de-confidentialite']) }}"
                class="transition-colors duration-200 link link-hover hover:text-cyan-200">@lang('Privacy policy')</a>
            <a href="{{ route('pages', ['page' => 'respect-environnement']) }}"
                class="transition-colors duration-200 link link-hover hover:text-cyan-200">@lang('Environmental protection')</a>
            <a href="{{ route('pages', ['page' => 'mandat-administratif']) }}"
                class="transition-colors duration-200 link link-hover hover:text-cyan-200">@lang('Administrative mandate')</a>
        </nav>
        <nav class="flex flex-col gap-2">
            <h6 class="mb-2 text-sm font-semibold tracking-wide text-cyan-100 uppercase">@lang('Contact')</h6>
            <a href="{{ route('contact') }}"
                class="transition-colors duration-200 link link-hover hover:text-cyan-200">@lang('Contact us')</a>
        </nav>
        <nav class="flex flex-col gap-2">
            <h6 class="mb-2 text-sm font-semibold tracking-wide text-cyan-100 uppercase">@lang('Social medias')</h6>
            <div class="flex gap-3">
                <a href="{{ $shop->facebook }}" target="_blank"
                    class="flex items-center justify-center w-10 h-10 transition-colors duration-200 rounded-full bg-white/10 hover:bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        class="fill-current">
                        <path
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z">
                        </path>
                    </svg>
                </a>
            </div>
        </nav>
    </div>
    <div class="py-4 text-sm text-center border-t text-cyan-100 border-white/10">
        &copy; {{ now()->year }} {{ $shop->name ?? config('app.name') }}. @lang('All rights reserved.')
    </div>
</footer>
