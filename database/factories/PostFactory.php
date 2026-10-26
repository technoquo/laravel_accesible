<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\PostStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        return [
            'author_id' => User::factory(),
            'category_id' => null,
            'status' => PostStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
        ]);
    }

    public function scheduledFuture(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => now()->addDay(),
        ]);
    }
}
