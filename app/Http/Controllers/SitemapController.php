<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('sitemap.xml', 3600, fn () => $this->buildXml());

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function buildXml(): string
    {
        $urls = [];

        foreach (['es', 'en'] as $locale) {
            $urls[] = [
                'loc' => route('home', ['locale' => $locale]),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];
            $urls[] = [
                'loc' => locale_route('articles.index', [], $locale),
                'changefreq' => 'daily',
                'priority' => '0.9',
            ];
            $urls[] = [
                'loc' => locale_route('about', [], $locale),
                'changefreq' => 'yearly',
                'priority' => '0.4',
            ];
            $urls[] = [
                'loc' => locale_route('contact', [], $locale),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ];
        }

        Post::published()
            ->with('translations:id,post_id,locale,slug')
            ->select('id', 'updated_at')
            ->get()
            ->each(function (Post $post) use (&$urls): void {
                foreach ($post->translations as $translation) {
                    $urls[] = [
                        'loc' => locale_route('articles.show', ['slug' => $translation->slug], $translation->locale),
                        'lastmod' => $post->updated_at->toDateString(),
                        'changefreq' => 'monthly',
                        'priority' => '0.8',
                    ];
                }
            });

        Category::with('translations:id,category_id,locale,slug')
            ->get()
            ->each(function (Category $category) use (&$urls): void {
                foreach ($category->translations as $translation) {
                    $urls[] = [
                        'loc' => locale_route('categories.show', ['slug' => $translation->slug], $translation->locale),
                        'changefreq' => 'weekly',
                        'priority' => '0.6',
                    ];
                }
            });

        Tag::with('translations:id,tag_id,locale,slug')
            ->get()
            ->each(function (Tag $tag) use (&$urls): void {
                foreach ($tag->translations as $translation) {
                    $urls[] = [
                        'loc' => locale_route('tags.show', ['slug' => $translation->slug], $translation->locale),
                        'changefreq' => 'weekly',
                        'priority' => '0.5',
                    ];
                }
            });

        return view('sitemap', compact('urls'))->render();
    }
}
