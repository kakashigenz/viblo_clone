<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Article::factory()->has(Tag::factory(3), 'tags')->create([
            'slug' => 'why-we-should-learn-python',
            'content' => 'Because Python is fun!',
            'title' => 'Why we should learn Python',
            'status' => Article::VISIBLE,
        ]);

        $point = rand(0, 50);
        $max_user = 20;
        Article::factory()->count(50)
            ->hasComments(5, [
                'user_id' => rand(1, $max_user)
            ])
            ->hasTags(3)
            ->hasVotes($point, [
                'user_id' => rand(1, $max_user)
            ])
            ->create([
                'point' => $point
            ]);
    }
}
