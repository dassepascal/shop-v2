<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
		'body',
		'post_id',
		'user_id',
		'parent_id',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function post(): BelongsTo
	{
		return $this->belongsTo(Post::class);
	}

	public function parent(): BelongsTo
	{
		return $this->belongsTo(Comment::class, 'parent_id');
	}

	public function children(): HasMany
	{
		return $this->hasMany(Comment::class, 'parent_id');
	}
}
