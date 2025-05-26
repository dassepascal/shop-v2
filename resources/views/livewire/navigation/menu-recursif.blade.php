<?php
namespace App\Http\Livewire\Navigation;

use App\Models\Menu;
use Livewire\Component;

class MenuRecursif extends Component
{
    public $menus;
    public $openMenus;

    public function mount($openMenus = []): void
    {
        $this->menus = Menu::with(['submenus' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();
        $this->openMenus = $openMenus;
    }

    public function toggleMenu($menuId): void
    {
        if (in_array($menuId, $this->openMenus)) {
            $this->openMenus = array_diff($this->openMenus, [$menuId]);
        } else {
            $this->openMenus[] = $menuId;
        }
    }
}
?>
<div>
@props(['openMenus' => []])

<ul class="space-y-1 ml-2">
    @foreach ($menus as $menu)
        <li>
            @if ($menu->submenus->isNotEmpty())
                <details {{ in_array($menu->id, $openMenus) ? 'open' : '' }}>
                    <summary wire:click="toggleMenu({{ $menu->id }})"
                             class="cursor-pointer font-medium text-gray-800 hover:text-blue-600">
                        {{ $menu->label }}
                    </summary>
                    <ul class="ml-4 mt-1 space-y-1 pl-2 border-l border-gray-300">
                        @foreach ($menu->submenus as $submenu)
                            <li>
                                <a href="{{ $submenu->link }}"
                                   class="text-sm text-blue-600 hover:underline {{ Str::startsWith($submenu->link ?? '', 'http') ? 'external' : '' }}">
                                    {{ $submenu->label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @else
                <a href="{{ $menu->link ?? '#' }}"
                   class="text-sm font-medium text-gray-800 hover:text-blue-600 {{ Str::startsWith($menu->link ?? '', 'http') ? 'external' : '' }}">
                    {{ $menu->label }}
                </a>
            @endif
        </li>
    @endforeach
</ul>

</div>

