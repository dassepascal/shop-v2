<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nbrPosts = 9;
		$nbrUsers = 3;

		foreach (range(1, $nbrPosts - 1) as $i) {
			$this->createComment($i, rand(1, $nbrUsers));
		}

		$comment = $this->createComment(2, 3);
		$this->createComment(2, 2, $comment->id);

		$comment = $this->createComment(2, 2);
		$this->createComment(2, 3, $comment->id);

		$comment = $this->createComment(2, 3, $comment->id);

		$comment = $this->createComment(2, 1, $comment->id);
		$this->createComment(2, 3, $comment->id);

		$comment = $this->createComment(4, 1);

		$comment = $this->createComment(4, 3, $comment->id);
		$this->createComment(4, 2, $comment->id);
		$this->createComment(4, 1, $comment->id);
	}

	protected function createComment($post_id, $user_id, $id = null)
	{
		return Comment::factory()->create([
			'post_id' => $post_id,
			'user_id' => $user_id,
			'parent_id' => $id,
		]);
	}
    }

