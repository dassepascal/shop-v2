<?php

use App\Models\Post;
use App\Models\User;
use Mary\Traits\Toast;
use App\Models\Comment;
use App\Models\BlogPage;
use Livewire\Volt\Component;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Title};
use Illuminate\Database\Eloquent\Builder;


new
    #[
        Title('List Posts'),
        Layout('components.layouts.admin')
    ]
    class extends Component {

        use Toast;

        public array $headersPosts;

        public bool $openGlance = true;


        public function mount(): void
        {
            $this->headersPosts = [['key' => 'date', 'label' => __('Date')], ['key' => 'title', 'label' => __('Title')]];
        }



        public function with(): array
        {
            $user    = Auth::user();
            $isRedac = $user->isRedac();
            $userId  = $user->id;

            return [

                'posts' => Post::select('id', 'title', 'slug', 'user_id', 'created_at', 'updated_at')->when($isRedac, fn(Builder $q) => $q->where('user_id', $userId))->latest()->get(),
                'pages' => BlogPage::all(),
                'users' => User::count(),
                'commentsNumber' => Comment::when($isRedac, fn(Builder $q) => $q->whereRelation('post', 'user_id', $userId))->count(),
                'comments'       => Comment::with('user', 'post:id,title,slug')->when($isRedac, fn(Builder $q) => $q->whereRelation('post', 'user_id', $userId))->latest()->take(5)->get(),
            ];
        }
    }; ?>

<div>
    <x-collapse wire:model="openGlance" class="shadow-md bg-red-500">
        <x-slot:heading>
            @lang('In a glance')
        </x-slot:heading>
        <x-slot:content class="flex flex-wrap gap-4">

            <a href="{{ route('admin.blog.posts.index') }}" class="flex-grow">
                <x-stat title="{{ __('Posts') }}" description="" value="{{ $posts->count() }}" icon="s-document-text"
                    class="shadow-hover" />
            </a>
            <a href="{{ route('admin.blog.pages.index') }}" class="flex-grow">
                <x-stat title="{{ __('Pages') }}" value="{{ $pages->count() }}" icon="s-document"
                    class="shadow-hover" />
            </a>
            <a href="{{ route('admin.blog.users.index') }}" class="flex-grow">
                <x-stat title="{{ __('Users') }}" value="{{ $users }}" icon="s-user"
                    class="shadow-hover" />
            </a>
            <a href="{{ route('admin.blog.comments.index') }}" class="flex-grow">
                <x-stat title="{{ __('Comments') }}" value="{{ $commentsNumber }}" icon="c-chat-bubble-left"
                    class="shadow-hover" />
            </a>


        </x-slot:content>
    </x-collapse>

    <br>
    @foreach ($comments as $comment)
    @if (!$comment->user->valid)
    <x-alert title="{!! __('Comment to valid from ') . $comment->user->name !!}" description="{!! $comment->body !!}" icon="c-chat-bubble-left"
        class="shadow-md alert-warning">
        <x-slot:actions>
            <x-button link="{{ route('admin.blog.comments.index') }}" label="{!! __('Show the comments') !!}" />
        </x-slot:actions>
    </x-alert>
    <br>
    @endif
    @endforeach

    <x-collapse class="shadow-md">
        <x-slot:heading>
            @lang('Recent posts')
        </x-slot:heading>
        <x-slot:content>
            <x-table :headers="$headersPosts" :rows="$posts->take(5)" striped>
                @scope('cell_date', $post)
                @lang('Created') {{ $post->created_at->diffForHumans() }}
                @if ($post->updated_at != $post->created_at)
                <br>
                @lang('Updated') {{ $post->updated_at->diffForHumans() }}
                @endif
                @endscope
                @scope('actions', $post)
                <x-popover>
                    <x-slot:trigger>
                        <x-button icon="s-document-text" link="{{ route('posts.show', $post->slug) }}" spinner class="btn-ghost btn-sm" />
                    </x-slot:trigger>
                    <x-slot:content class="pop-small">
                        @lang('Show post')
                    </x-slot:content>
                </x-popover>
                @endscope
            </x-table>
        </x-slot:content>
    </x-collapse>
</div>
