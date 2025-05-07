<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPage extends Model
{
    use HasFactory;

	public $timestamps  = false;
	protected $fillable = [
		'title',
		'slug',
		'body',
		'active',
		'seo_title',
		'meta_description',
		'meta_keywords',
	];
}
