<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /** @var array<int, array{key: string, es: string, en: string}> */
    private array $tags = [
        ['key' => 'wcag', 'es' => 'WCAG', 'en' => 'WCAG'],
        ['key' => 'aria', 'es' => 'ARIA', 'en' => 'ARIA'],
        ['key' => 'blade', 'es' => 'Blade', 'en' => 'Blade'],
        ['key' => 'livewire', 'es' => 'Livewire', 'en' => 'Livewire'],
        ['key' => 'axe-core', 'es' => 'axe-core', 'en' => 'axe-core'],
    ];

    public function run(): void
    {
        foreach ($this->tags as $data) {
            $tag = Tag::firstOrCreate(['stable_key' => $data['key']]);

            foreach (['es', 'en'] as $locale) {
                TagTranslation::firstOrCreate(
                    ['tag_id' => $tag->id, 'locale' => $locale],
                    [
                        'name' => $data[$locale],
                        'slug' => Str::slug($data[$locale]),
                    ]
                );
            }
        }
    }
}
