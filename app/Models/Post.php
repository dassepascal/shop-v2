<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'body', 'active', 'image', 'user_id', 'category_id', 'seo_title', 'meta_description', 'meta_keywords', 'pinned'];

    public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function category(): BelongsTo
	{
		return $this->belongsTo(Category::class);
	}
    public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	public function validComments(): HasMany
	{
		return $this->comments()->whereHas('user', function ($query) {
			$query->whereValid(true);
		});
	}

    public function favoritedByUsers(): BelongsToMany
{
	return $this->belongsToMany(User::class, 'favorites');
}
}
