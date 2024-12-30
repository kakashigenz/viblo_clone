<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->sentence(8),
            'user_id' => rand(0, 50),
            'parent_id' => null,
            'point' => fake()->numberBetween(0, 100),
            'is_visible' => Comment::VISIBLE,
        ];
    }
}
