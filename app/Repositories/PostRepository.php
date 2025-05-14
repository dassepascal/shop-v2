<?php

declare(strict_types=1);

namespace App\Repositories;

use Exception;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository
{
    public function getPostsPaginate(?Category $category): LengthAwarePaginator
    {
        $query = $this->getBaseQuery()->orderBy('pinned', 'desc')->latest();

        if ($category) {
            $query->whereBelongsTo($category);
        }

        return $query->paginate(config('app.pagination'));
    }

    protected function getBaseQuery(): Builder
    {
        $specificReqs = [
            'mysql'  => "LEFT(body, LOCATE(' ', body, 700))",
            'sqlite' => 'substr(body, 1, 700)',
            'pgsql'  => 'substring(body from 1 for 700)',
        ];

        $usedDbSystem = env('DB_CONNECTION', 'mysql');

        if (!isset($specificReqs[$usedDbSystem])) {
            throw new Exception("Base de données non supportée: {$usedDbSystem}");
        }

        $adaptedReq = $specificReqs[$usedDbSystem];

        return Post::select('id', 'slug', 'image', 'title', 'user_id', 'category_id', 'created_at', 'pinned')
            ->selectRaw(
                "CASE
                    WHEN LENGTH(body) <= 300 THEN body
                    ELSE {$adaptedReq}
                END AS excerpt",
            )
            ->with('user:id,name', 'category')
            ->whereActive(true);
    }



    public function search(string $search): LengthAwarePaginator
    {
        return $this->getBaseQuery()
            ->latest()
            ->where(function ($query) use ($search) {
                $query->where('body', 'like', "%{$search}%")->orWhere('title', 'like', "%{$search}%");
            })
            ->paginate(config('app.pagination'));
    }



    public function getPostBySlug(string $slug): Post
    {
        $userId = auth()->id();

        return Post::with('user:id,name', 'category')
                ->withCount('validComments')
                ->withExists([
                    'favoritedByUsers as is_favorited' => function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    },
                ])
                ->where('slug', $slug)->firstOrFail();
    }

    public function generateUniqueSlug(string $slug): string
{
	$newSlug = $slug;
	$counter = 1;
	while (Post::where('slug', $newSlug)->exists()) {
		$newSlug = $slug . '-' . $counter;
		++$counter;
	}
	return $newSlug;
}

public function clonePost(int $postId): void
{
    $originalPost       = Post::findOrFail($postId);
    $clonedPost         = $originalPost->replicate();
    $postRepository     = new PostRepository();
    $clonedPost->slug   = $postRepository->generateUniqueSlug($originalPost->slug);
    $clonedPost->active = false;
    $clonedPost->save();

    // Ici on redirigera vers le formulaire de modification de l'article cloné
}

}
