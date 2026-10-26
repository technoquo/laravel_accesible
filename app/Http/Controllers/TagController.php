<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\TagTranslation;
use Illuminate\View\View;

class TagController extends Controller
{
    public function show(string $locale, string $slug): View
    {
        $tagTranslation = TagTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->firstOrFail();

        $tag = Tag::with([
            'translations' => fn ($q) => $q->where('locale', $locale)
                ->select('id', 'tag_id', 'locale', 'name', 'slug'),
        ])->findOrFail($tagTranslation->tag_id);

        $posts = $tag->posts()
            ->publishedForLocale($locale)
            ->with([
                'translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'post_id', 'locale', 'title', 'slug', 'excerpt'),
                'category.translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'category_id', 'locale', 'name', 'slug'),
            ])
            ->select('posts.id', 'posts.category_id', 'posts.published_at')
            ->latest('posts.published_at')
            ->paginate(10)
            ->withQueryString();

        $alternateLocale = $locale === 'es' ? 'en' : 'es';
        $alternateTagTranslation = $tag->translations->firstWhere('locale', $alternateLocale);
        $alternateUrls = [
            $alternateLocale => $alternateTagTranslation
                ? locale_route('tags.show', ['slug' => $alternateTagTranslation->slug], $alternateLocale)
                : locale_route('articles.index', [], $alternateLocale),
        ];

        return view('tags.show', compact('tag', 'tagTranslation', 'posts', 'alternateUrls', 'locale'));
    }
}
