<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stable_key' => fake()->unique()->slug(2),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
