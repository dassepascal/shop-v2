<?php

use App\Models\Submenu;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Traits\ManageMenus;

new #[Title('Edit Submenu'), Layout('components.layouts.admin')]
class extends Component {
	use Toast, ManageMenus;

	public Submenu $submenu;
	public string $sublabel = '';
	public string $sublink  = '';
	public int $subPost     = 0;
	public int $subPage     = 0;
	public int $subCategory = 0;
	public int $subOption   = 1;

	public function mount(Submenu $submenu): void
	{
		$this->submenu = $submenu;
		$this->sublabel = $submenu->label;
		$this->sublink  = $submenu->link;
		$this->search();
	}

	public function saveSubmenu($menu = null): void
	{
		$data = $this->validate([
			'sublabel' => ['required', 'string', 'max:255'],
			'sublink'  => 'required|regex:/\/([a-z0-9_-]\/*)*[a-z0-9_-]*/',
		]);

		$this->submenu->update([
			'label' => $data['sublabel'],
			'link'  => $data['sublink'],
		]);

		$this->success(__('Menu updated with success.'), redirectTo: '/admin/blog/menus/index');
	}

}; ?>

<div>
    <x-header title="{{ __('Edit a submenu') }}" separator progress-indicator>
        <x-slot:actions class="lg:hidden">
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline"
                link="{{ route('admin.dashboard') }}" />
        </x-slot:actions>
    </x-header>
    <x-card>
		@include('livewire.admin.blog.menus.submenu-form')
    </x-card>
</div>
