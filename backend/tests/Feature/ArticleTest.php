<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected array $exam_data;
    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exam_data = [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'tags' => ['fashion', 'life'],
            'status' => fake()->randomElement([Article::VISIBLE, Article::DRAFT])
        ];
        $this->article = Article::factory()->create();
    }

    public function test_create_article_with_anonymous_user(): void
    {
        $response = $this->postJson(route('article.store'), $this->exam_data);
        $response->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_create_article_with_empty_data(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('article.store'), []);
        $response->assertUnprocessable()->assertInvalid([
            'title' => __('validation.required', ['attribute' => 'title']),
            'tags' => __('validation.required', ['attribute' => 'tags']),
            'content' => __('validation.required', ['attribute' => 'content']),
            'status' => __('validation.required', ['attribute' => 'status'])
        ]);
    }

    public function test_create_article_with_quantity_invalid_tag(): void
    {
        $this->exam_data['tags'] = fake()->unique()->words(10);
        $response = $this->actingAs($this->user)->postJson(route('article.store'), $this->exam_data);
        $response->assertUnprocessable()->assertInvalid([
            'tags' => __('validation.between.array', ['attribute' => 'tags', 'min' => 1, 'max' => 5]),
        ]);
    }

    public function test_create_article_with_invalid_status(): void
    {
        $this->exam_data['status'] = -5654;
        $response = $this->actingAs($this->user)->postJson(route('article.store'), $this->exam_data);
        $response->assertUnprocessable()->assertInvalid([
            'status' => __('validation.in', ['attribute' => 'status']),
        ]);
    }

    public function test_create_article_with_valid_data(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('article.store'), $this->exam_data);
        $response->assertCreated()->assertJsonStructure([
            'title',
            'slug',
            'content',
            'user_id',
        ]);
    }

    public function test_create_article_with_another_user(): void
    {
        $article = $this->article->toArray();
        $article['tags'] = fake()->unique()->words();
        $response = $this->actingAs($this->user)->putJson(route('article.update', ['slug' => $this->article->slug]), $article);
        $response->assertForbidden()->assertJsonStructure(['message']);
    }

    public function test_edit_article_with_empty_data(): void
    {
        $author = $this->article->user;
        $response = $this->actingAs($author)->putJson(route('article.update', ['slug' => $this->article->slug]), []);
        $response->assertUnprocessable()->assertInvalid([
            'title' => __('validation.required', ['attribute' => 'title']),
            'tags' => __('validation.required', ['attribute' => 'tags']),
            'content' => __('validation.required', ['attribute' => 'content']),
            'status' => __('validation.required', ['attribute' => 'status'])
        ]);
    }

    public function test_edit_article_with_quantity_invalid_tag(): void
    {
        $article = $this->article->toArray();
        $article['tags'] = fake()->unique()->words(10);
        $response = $this->actingAs($this->user)->putJson(route('article.update', ['slug' => data_get($article, 'slug')]), $article);
        $response->assertUnprocessable()->assertInvalid([
            'tags' => __('validation.between.array', ['attribute' => 'tags', 'min' => 1, 'max' => 5]),
        ]);
    }

    public function test_edit_article_with_invalid_status(): void
    {
        $this->article['status'] = -5654;
        $author = $this->article->user;
        $response = $this->actingAs($this->user)->putJson(route('article.update', ['slug' => $this->article->slug]), $this->article->toArray());
        $response->assertUnprocessable()->assertInvalid([
            'status' => __('validation.in', ['attribute' => 'status']),
        ]);
    }

    public function test_edit_article_with_valid_data(): void
    {
        $author = $this->article->user;
        $article = $this->article->toArray();
        $article['tags'] = fake()->unique()->words();
        $response = $this->actingAs($author)->putJson(route('article.update', ['slug' => data_get($article, 'slug')]), $article);
        $response->assertOk()->assertJsonStructure([
            'title',
            'slug',
            'content',
            'user_id',
        ]);
    }

    public function test_delete_article_with_another_user(): void
    {
        $response = $this->actingAs($this->user)->deleteJson(route('article.delete', ['slug' => $this->article->slug]));
        $response->assertForbidden()->assertJsonStructure([
            'message'
        ]);
    }

    public function test_delete_successfully_article(): void
    {
        $author = $this->article->user;
        $response = $this->actingAs($author)->deleteJson(route('article.delete', ['slug' => $this->article->slug]));
        $response->assertOk()->assertJsonStructure([
            'message'
        ]);
    }
}
