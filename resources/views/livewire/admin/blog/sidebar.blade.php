<div>
    <x-menu activate-by-route>

        <x-menu-separator />

        <x-menu-item title="{{ __('Dashboard') }}" icon="s-document-text" link="{{ route('admin.dashboard') }}" />
        <x-menu-item title="{{ __('Dashboard blog') }}" icon="s-document-text"
            link="{{ route('admin.blog.dashboard') }}" />

        <x-menu-sub title="{{ __('Posts') }}" icon="s-document-text">
            <x-menu-item title="{{ __('All posts') }}" link="{{ route('admin.blog.posts.index') }}" />
            <x-menu-item title="{{ __('Add a post') }}" link="{{ route('admin.blog.posts.create') }}" />
            @if (Auth::user()->isAdmin())
                <x-menu-item title="{{ __('Categories') }}" link="{{ route('admin.blog.categories.index') }}" />
            @endif


        </x-menu-sub>

        @if (Auth::user()->isAdmin())
            <x-menu-sub title="{{ __('Pages') }}" icon="s-document">
                <x-menu-item title="{{ __('All pages') }}" link="{{ route('admin.blog.pages.index') }}" />
                <x-menu-item title="{{ __('Add a page') }}" link="{{ route('admin.blog.pages.create') }}" />
            </x-menu-sub>
            <x-menu-item icon="s-user" title="{{ __('Accounts') }}" link="{{ route('admin.blog.users.index') }}" />
            <x-menu-item icon="c-chat-bubble-left" title="{{ __('Comments') }}"
            link="{{ route('admin.blog.comments.index') }}" />
        <x-menu-sub title="{{ __('Menus') }}" icon="m-list-bullet">
            <x-menu-item title="{{ __('Navbar') }}" link="{{ route('admin.blog.menus.index') }}" />
            <x-menu-item icon="c-photo" title="{{ __('Images') }}" link="{{ route('admin.blog.images.index') }}" />

        </x-menu-sub>
        <x-menu-item icon="m-cog-8-tooth" title="{{ __('Settings') }}" link="{{ route('admin.blog.settings') }}" :class="App::isDownForMaintenance() ? 'bg-red-300' : ''" />
        @endif

        <x-menu-separator />
      
        <x-menu-item icon="m-arrow-right-end-on-rectangle" title="{{ __('Go on blog') }}" link="/blog" />
        <x-menu-item>
            <x-theme-toggle />
        </x-menu-item>

    </x-menu>
</div>
