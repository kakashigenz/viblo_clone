<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
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
        Article::factory()->count(50)->has(Tag::factory(3), 'tags')->create();
    }
}
