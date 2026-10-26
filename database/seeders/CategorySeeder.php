<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /** @var array<int, array{key: string, es: string, en: string}> */
    private array $categories = [
        ['key' => 'formularios', 'es' => 'Formularios', 'en' => 'Forms'],
        ['key' => 'teclado', 'es' => 'Teclado', 'en' => 'Keyboard'],
        ['key' => 'lectores-pantalla', 'es' => 'Lectores de pantalla', 'en' => 'Screen readers'],
        ['key' => 'pruebas', 'es' => 'Pruebas', 'en' => 'Testing'],
    ];

    public function run(): void
    {
        foreach ($this->categories as $index => $data) {
            $category = Category::firstOrCreate(
                ['stable_key' => $data['key']],
                ['sort_order' => $index]
            );

            foreach (['es', 'en'] as $locale) {
                CategoryTranslation::firstOrCreate(
                    ['category_id' => $category->id, 'locale' => $locale],
                    [
                        'name' => $data[$locale],
                        'slug' => Str::slug($data[$locale]),
                        'description' => null,
                    ]
                );
            }
        }
    }
}
