<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PostTranslation>
 */
class PostTranslationFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'post_id' => Post::factory(),
            'locale' => 'es',
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'markdown_content' => "# {$title}\n\n".fake()->paragraphs(3, true),
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function forLocale(string $locale): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => $locale,
        ]);
    }
}
