<?php

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;

new
    #[
        Title('List Posts'),
        Layout('components.layouts.admin')
    ]
    class extends Component {


        public function headers(): array
        {
            $headers = [['key' => 'title', 'label' => __('Title')]];

            if (Auth::user()->isAdmin()) {
                $headers = array_merge($headers, [['key' => 'user_name', 'label' => __('Author')]]);
            }

            return array_merge($headers, [['key' => 'category_title', 'label' => __('Category')], ['key' => 'comments_count', 'label' => __('')], ['key' => 'active', 'label' => __('Published')], ['key' => 'date', 'label' => __('Date')]]);
        }

        public function posts()
        {
            return Post::query()
                ->select('id', 'title', 'slug', 'category_id', 'active', 'user_id', 'created_at', 'updated_at')
                ->when(Auth::user()->isAdmin(), fn(Builder $q) => $q->withAggregate('user', 'name'))
                ->when(!Auth::user()->isAdmin(), fn(Builder $q) => $q->where('user_id', Auth::id()))
                ->withAggregate('category', 'title')
                ->withcount('comments')
                ->latest()
                ->get();
        }

        public function with(): array
        {
            return [
                'posts'   => $this->posts(),
                'headers' => $this->headers(),
            ];
        }
    }; ?>

<div>
    <div class="bg-green-500">
    <x-header title="{{ __('Posts') }}" separator progress-indicator>

    </x-header>
    </div>


    @if ($posts->count() > 0)
    <x-card>
        <div class="w-full overflow-x-auto">
            <x-table
                striped
                :headers="$headers"
                :rows="$posts"
                per-page="perPage"
                link="#">
                @scope('header_comments_count', $header)
                {{ $header['label'] }}
                <x-icon name="c-chat-bubble-left" />
                @endscope

                @scope('cell_user.name', $post)
                {{ $post->user->name }}
                @endscope
                @scope('cell_category.title', $post)
                {{ $post->category->title }}
                @endscope
                @scope('cell_comments_count', $post)
                @if ($post->comments_count > 0)
                <x-badge value="{{ $post->comments_count }}" class="badge-primary" />
                @endif
                @endscope
                @scope('cell_active', $post)
                @if ($post->active)
                <x-icon name="o-check-circle" />
                @endif
                @endscope
                @scope('cell_date', $post)
                @lang('Created') {{ $post->created_at->diffForHumans() }}
                @if ($post->updated_at != $post->created_at)
                <br>
                @lang('Updated') {{ $post->updated_at->diffForHumans() }}
                @endif
                @endscope
            </x-table>
        </div>
    </x-card>
    @endif
</div>
