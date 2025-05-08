<?php
use Livewire\Volt\Component;

new class extends Component {
    public string $query = '';

    public function search(): void
    {
        if (!empty($this->query)) {
            $this->redirectRoute('posts.search', ['param' => $this->query]);
        }
    }
};
?>

<div>
    <form wire:submit.prevent="search" class="flex items-center space-x-2">
        <x-input wire:model.live="query" placeholder="{{ __('Search posts...') }}" class="input-bordered" />
        <x-button type="submit" icon="o-magnifying-glass" class="btn-primary btn-sm" />
    </form>
</div>
