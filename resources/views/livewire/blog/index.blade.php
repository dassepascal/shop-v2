<?php
use App\Models\Category;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use WithPagination;

    public ?Category $category = null;
    public string $param = '';

    public function mount(string $slug = '', string $param = ''): void
    {
        $this->param = $param;

        if (request()->is('category/*')) {
            $this->category = $this->getCategoryBySlug($slug);
        }
    }

    public function getPosts(): LengthAwarePaginator
    {
        $postRepository = new PostRepository();

        if (!empty($this->param)) {
            return $postRepository->search($this->param);
        }

        return $postRepository->getPostsPaginate($this->category);
    }

    protected function getCategoryBySlug(string $slug): ?Category
    {
        return 'category' === request()->segment(1) ? Category::whereSlug($slug)->firstOrFail() : null;
    }

    public function with(): array
    {
        return ['posts' => $this->getPosts()];
    }
};
?>

<div>
    <div class="min-h-[35vw] hero" style="background-image: url({{ asset('storage/hero.jpg') }})">
        <div class="bg-opacity-60 hero-overlay"></div>
        <a href="{{ route('home') }}">
            <div class="text-center hero-content text-neutral-content">
                <div>
                    <h1 class="mb-5 text-4xl font-bold sm:text-5xl md:text-6xl lg:text-7xl xl:text-8xl">
                        Blog
                    </h1>
                    <p class="mb-5 text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl">
                        sous titre
                    </p>
                </div>
            </div>
        </a>
    </div>
    <div class="relative grid items-center w-full py-5 mx-auto prose md:px-12 max-w-7xl mt-5">
        @if ($category)
            <x-header title="{{ __('Posts for category ') }} {{ $category->title }}" size="text-2xl sm:text-3xl md:text-4xl" />
        @elseif ($param !== '')
            <x-header title="{{ __('Posts for search ') }} '{{ $param }}'" size="text-2xl sm:text-3xl md:text-4xl" />
        @else
            <x-header title="{{ __('All Posts') }}" size="text-2xl sm:text-3xl md:text-4xl" />
        @endif

        <div class="mb-4 mary-table-pagination">
            <div class="mb-5 border border-t-0 border-x-0 border-b-1 border-b-base-300"></div>
            {{ $posts->links() }}
        </div>

        <div class="container mx-auto">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($posts as $post)
                    <x-card
                        class="w-full transition duration-500 ease-in-out shadow-md shadow-gray-500 hover:shadow-xl hover:shadow-gray-500"
                        title="{!! $post->title !!}">
                        <div class="text-justify">{!! str(strip_tags($post->excerpt))->words(config('app.excerptSize')) !!}</div>
                        <br>
                        <hr>
                        <div class="flex justify-between">
                            <p class="text-left cursor-pointer">{{ $post->user->name }}</p>
                            <p class="text-right"><em>{{ $post->created_at->isoFormat('LL') }}</em></p>
                        </div>
                        @if ($post->image)
                            <x-slot:figure>
                                <a href="{{ route('posts.show', $post->slug) }}">
                                    <img src="{{ asset('storage/photos/' . $post->image) }}" alt="{{ $post->title }}" />
                                </a>
                            </x-slot:figure>
                        @endif
                        <x-slot:menu>
                            @if ($post->pinned)
                                <x-badge value="{{ __('Pinned') }}" class="p-3 badge-warning" />
                            @endif
                        </x-slot:menu>
                        <x-slot:actions>
                            <div class="flex flex-col items-end space-y-2 sm:items-start sm:flex-row sm:space-y-0 sm:space-x-2">
                                <x-popover>
                                    <x-slot:trigger>
                                        <x-button label="{{ $post->category->title }}"
                                            link="{{ route('blog.index', ['slug' => $post->category->slug]) }}" class="mt-1 btn-outline btn-sm" />
                                    </x-slot:trigger>
                                    <x-slot:content class="pop-small">
                                        @lang('Show this category')
                                    </x-slot:content>
                                </x-popover>
                                <x-popover>
                                    <x-slot:trigger>
                                        <x-button label="{{ __('Read') }}" link="{{ route('posts.show', $post->slug) }}"
                                            class="mt-1 btn-outline btn-sm" />
                                    </x-slot:trigger>
                                    <x-slot:content class="pop-small">
                                        @lang('Read this post')
                                    </x-slot:content>
                                </x-popover>
                            </div>
                        </x-slot:actions>
                    </x-card>
                @empty
                    <div class="col-span-3">
                        <x-card title="{{ __('Nothing to show !') }}">
                            {{ __('No Post found with these criteria') }}
                        </x-card>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="mb-4 mary-table-pagination">
            <div class="mb-5 border border-t-0 border-x-0 border-b-1 border-b-base-300"></div>
            {{ $posts->links() }}
        </div>
    </div>
</div>
