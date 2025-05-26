<?php

use App\Models\BlogPage;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Pages'), Layout('components.layouts.admin')]
class extends Component {
	use Toast, WithPagination;

	public function headers(): array
	{
		return [['key' => 'title', 'label' => __('Title')], ['key' => 'slug', 'label' => 'Slug'], ['key' => 'active', 'label' => __('Published')]];
	}

	public function deletePage(BlogPage $page): void
	{
		$page->delete();
		$this->success(__('Page deleted'));
	}

	public function with(): array
	{
		return [
			'pages'   => BlogPage::select('id', 'title', 'slug', 'active')->get(),
			'headers' => $this->headers(),
		];
	}
}; ?>

<div>
    <x-header title="{{ __('Pages') }}" separator progress-indicator >
        <x-slot:actions class="lg:hidden">
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline"
                link="{{ route('admin.blog.dashboard') }}" />
            <x-button icon="c-document-plus" label="{{ __('Add a page') }}" class="btn-outline"
                link="{{ route('admin.blog.pages.create') }}" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-table striped :headers="$headers" :rows="$pages" link="/admin/blog/pages/{slug}/edit">
            @scope('cell_active', $page)
                @if ($page->active)
                    <x-icon name="o-check-circle" />
                @endif
            @endscope
            @scope('actions', $page)
                <x-popover>
                    <x-slot:trigger>
                        <x-button icon="o-trash" wire:click="deletePage({{ $page->id }})"
                            wire:confirm="{{ __('Are you sure to delete this page?') }}" spinner
                            class="text-red-500 btn-ghost btn-sm" />
                    </x-slot:trigger>
                    <x-slot:content class="pop-small">
                        @lang('Delete')
                    </x-slot:content>
                </x-popover>
            @endscope
        </x-table>
    </x-card>
</div>
