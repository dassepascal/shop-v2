<?php

namespace Database\Seeders;

use App\Models\BlogPage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BlogPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
			['slug' => 'terms', 'title' => 'Terms'],
			['slug' => 'privacy-policy', 'title' => 'Privacy Policy'],
		];

		foreach ($items as $item) {
			BlogPage::factory()->create([
				'title'     => $item['title'],
				'seo_title' => 'Page ' . $item['title'],
				'slug'      => $item['slug'],
				'active'    => true,
			]);
		}
	}
    }

