<?php

use App\Models\Menu;
use Mary\Traits\Toast;
use Illuminate\Support\Str;
use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\{Layout, Title};

new #[Title('Edit menu'), Layout('components.layouts.admin')]
class extends Component {
	use Toast;

	public Menu $menu;
	public string $label = '';
	public ?string $link = null;

	public function mount(Menu $menu): void
	{
		$this->menu = $menu;
		$this->fill($this->menu);
        if (Str::startsWith($this->link, '/blog/blog')) {
            $this->link = Str::replaceFirst('/blog/blog', '/blog', $this->link);
        }
	}

	public function save(): void
	{
		$data = $this->validate([
			'label' => ['required', 'string', 'max:255', Rule::unique('menus')->ignore($this->menu->id)],
			'link'  => 'nullable|regex:/\/([a-z0-9_-]\/*)*[a-z0-9_-]*/',
		]);
        dd($data);

		$this->menu->update($data);

		$this->success(__('Menu updated with success.'), redirectTo: '/admin/blog/menus/index');
	}
}; ?>

<div>
    <x-header title="{{ __('Edit a menu') }}" separator progress-indicator>
        <x-slot:actions class="lg:hidden">
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline"
                link="{{ route('admin.dashboard') }}" />
        </x-slot:actions>
    </x-header>
    <x-card>
        <x-form wire:submit="save">
            <x-input label="{{ __('Title') }}" wire:model="label" />
            <x-input type="text" wire:model="link" label="{{ __('Link') }}" />
            <x-slot:actions>
                <x-button label="{{ __('Save') }}" icon="o-paper-airplane" spinner="save" type="submit"
                    class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
