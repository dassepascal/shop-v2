<div>
    <x-menu activate-by-route>
        <x-menu-separator />
        <x-menu-item title="{{ __('Dashboard') }}" icon="s-document-text" link="{{ route('admin.blog.dashboard') }}" />
        <x-menu-sub title="{{ __('Posts') }}" icon="s-document-text">
            <x-menu-item title="{{ __('All posts') }}" link="{{ route('admin.blog.posts.index') }}" />
            <x-menu-item title="{{ __('Add a post') }}" link="{{ route('admin.blog.posts.create') }}" />
        </x-menu-sub>
        <x-menu-sub title="{{ __('Categories') }}" icon="s-folder">
            <x-menu-item title="{{ __('All categories') }}" link="{{ route('admin.blog.categories.index') }}" />
            <x-menu-item title="{{ __('Add a category') }}" link="{{ route('admin.blog.categories.create') }}" />
        </x-menu-sub>
        <x-menu-separator />
        <x-menu-item title="{{ __('Settings') }}" icon="s-cog-8-tooth" link="#" />
    </x-menu>
</div>
