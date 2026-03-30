<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public string $group = ''; // Propriété pour l'accordéon

    // Propriété pour la recherche par mot clé
    public string $search = '';

    public function with(): array
    {
        $query = Product::whereActive(true)->with(['images', 'features']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('features', function ($q2) {
                      $q2->where('value', 'like', '%' . $this->search . '%')
                         ->orWhere('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return [
            'products' => $query->get(),
        ];
    }

    // Méthode pour réinitialiser les filtres
    public function resetFilters(): void
    {
        $this->search = '';
    }
};
?>

<div class="container mx-auto">
    @if (session('registered'))
        <x-alert title="{!! session('registered') !!}" icon="s-rocket-launch" class="mb-4 alert-info" dismissible />
    @endif
    <x-card class="w-full shadow-md shadow-gray-500" shadow separator>
        {!! $shop->home !!}
    </x-card>

    <!-- Barre de recherche par mot clé -->
    <div class="mt-4 mb-6 flex flex-wrap gap-4 items-end">
        <x-input
            wire:model.live="search"
            label="{{ __('Search') }}"
            placeholder="{{ __('Search by keyword...') }}"
            class="flex-1"
        />
        <x-button wire:click="resetFilters" class="btn-secondary" icon="o-x-mark">{{ __('Reset') }}</x-button>
    </div>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($products as $product)
            @php
            $bestPrice = getBestPrice($product);
            $hasPromotion = $bestPrice < $product->price;
            if ($hasPromotion) {
                $titleContent = '<span class="line-through">' . number_format($product->price, 2, ',', ' ') . ' € TTC</span> <span class="text-red-500">' . number_format($bestPrice, 2, ',', ' ') . ' € TTC</span>';
            } else {
                $titleContent = '<span>' . number_format($product->price, 2, ',', ' ') . ' € TTC</span>';
            }
            $firstImage = $product->images->first()?->image ?? 'ask.png'; // Première image ou image par défaut
            @endphp
            <x-card class="shadow-md transition duration-500 ease-in-out shadow-gray-500 hover:shadow-xl hover:shadow-gray-500 flex flex-col justify-between relative">
                <b>{!! $product->name !!} :</b>
                {!! $titleContent !!}<br>

                @unless ($product->quantity)
                    <br><span class="text-red-500">@lang('Product out of stock')</span>
                @endunless

                <x-slot:figure>
                    <div class="relative group">
                        @if ($product->quantity)
                            <a href="{{ route('products.show', $product) }}" class="block">
                                <img src="{{ asset('storage/photos/' . $firstImage) }}" alt="{!! $product->name !!}" class="w-full object-cover" />
                            </a>
                        @else
                            <img src="{{ asset('storage/photos/' . $firstImage) }}" alt="{!! $product->name !!}" class="w-full object-cover" />
                        @endif

                        <!-- Conteneur pour les caractéristiques avec titre, affiché au survol -->
                        @if($product->features->isNotEmpty())
                            <div class="absolute inset-0 bg-black bg-opacity-75 text-white p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center pointer-events-none">
                                <h3 class="text-lg font-semibold mb-2">{{ __('Features') }}</h3>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($product->features as $feature)
                                        <li>{{ $feature->name }} : {{ $feature->pivot->value }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </x-slot:figure>
            </x-card>
        @endforeach
    </div>
</div>
