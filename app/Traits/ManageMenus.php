<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use App\Models\{Category, Page, Post};

trait ManageMenus
{
    public ?int $post_id = null;
    public Collection $postsSearchable;

    public function search(string $value = ''): void
    {
        $selectedOption = Post::select('id', 'title')->where('id', $this->post_id)->get();

        $this->postsSearchable = Post::query()
            ->select('id', 'title')
            ->where('title', 'like', "%{$value}%")
            ->orderBy('title')
            ->take(5)
            ->get()
            ->merge($selectedOption);
    }

    public function changeSelection($value): void
    {
        $this->updateSubProperties(['model' => Post::class, 'route' => 'posts.show'], $value);
    }

    public function updating($property, $value): void
    {
        if ($value === '') {
            return;
        }

        $modelMap = [
            'subPage' => ['model' => Page::class, 'route' => 'pages.show'],
            'subCategory' => ['model' => Category::class, 'route' => 'category'],
        ];

        if (array_key_exists($property, $modelMap)) {
            $this->updateSubProperties($modelMap[$property], $value);
        } elseif ($property === 'subOption') {
            $this->resetSubProperties();
            $this->search();
        }
    }

    private function updateSubProperties($modelInfo, $value): void
    {
        $model = $modelInfo['model']::find($value);
        if ($model) {
            $this->sublabel = $model->title;
            $this->sublink = $modelInfo['route'] === 'posts.show' || $modelInfo['route'] === 'pages.show'
                ? route($modelInfo['route'], $model->slug)
                : url($modelInfo['route'] . '/' . $model->slug);
        }
    }

    private function resetSubProperties(): void
    {
        $this->sublabel = '';
        $this->sublink = '';
        $this->subPost = 0;
        $this->subPage = 0;
        $this->subCategory = 0;
    }

    public function with(): array
    {
        return [
            'pages'      => Page::select('id', 'title', 'slug')->get(),
            'categories' => Category::all(),
            'subOptions' => [['id' => 1, 'name' => __('Post')], ['id' => 2, 'name' => __('Page')], ['id' => 3, 'name' => __('Category')]],
        ];
    }
}
