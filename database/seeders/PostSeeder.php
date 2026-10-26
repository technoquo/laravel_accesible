<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\Tag;
use App\Models\User;
use App\PostStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->firstOrFail();
        $tags = Tag::all();

        $articles = [
            [
                'es' => ['title' => 'Formularios Blade accesibles', 'excerpt' => 'Cómo construir formularios Blade con etiquetas, errores y foco accesibles.'],
                'en' => ['title' => 'Accessible Blade Forms', 'excerpt' => 'How to build Blade forms with accessible labels, errors, and focus management.'],
            ],
            [
                'es' => ['title' => 'Mensajes de error accesibles', 'excerpt' => 'Técnicas para comunicar errores de validación de forma accesible.'],
                'en' => ['title' => 'Accessible Error Messages', 'excerpt' => 'Techniques for communicating validation errors accessibly.'],
            ],
        ];

        foreach ($articles as $data) {
            $post = Post::create([
                'author_id' => $admin->id,
                'status' => PostStatus::Draft,
                'published_at' => null,
            ]);

            foreach (['es', 'en'] as $locale) {
                $title = $data[$locale]['title'];
                PostTranslation::create([
                    'post_id' => $post->id,
                    'locale' => $locale,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'excerpt' => $data[$locale]['excerpt'],
                    'markdown_content' => "# {$title}\n\n".$data[$locale]['excerpt'],
                ]);
            }

            if ($tags->isNotEmpty()) {
                $post->tags()->attach($tags->random(min(2, $tags->count()))->pluck('id'));
            }
        }
    }
}
