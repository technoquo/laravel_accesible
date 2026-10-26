<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(string $locale, string $slug): View
    {
        $categoryTranslation = CategoryTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->firstOrFail();

        $alternateLocale = $locale === 'es' ? 'en' : 'es';

        $category = Category::with([
            'translations' => fn ($q) => $q->whereIn('locale', [$locale, $alternateLocale])
                ->select('id', 'category_id', 'locale', 'name', 'slug', 'description'),
        ])->findOrFail($categoryTranslation->category_id);

        $posts = $category->posts()
            ->publishedForLocale($locale)
            ->with([
                'translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'post_id', 'locale', 'title', 'slug', 'excerpt'),
                'tags.translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'tag_id', 'locale', 'name', 'slug'),
            ])
            ->select('posts.id', 'posts.category_id', 'posts.published_at')
            ->latest('posts.published_at')
            ->paginate(10)
            ->withQueryString();

        $alternateCategoryTranslation = $category->translations->firstWhere('locale', $alternateLocale);
        $alternateUrls = [
            $alternateLocale => $alternateCategoryTranslation
                ? locale_route('categories.show', ['slug' => $alternateCategoryTranslation->slug], $alternateLocale)
                : locale_route('articles.index', [], $alternateLocale),
        ];

        $baseUrl = locale_route('categories.show', ['slug' => $categoryTranslation->slug]);
        $canonicalUrl = $posts->currentPage() > 1
            ? $baseUrl.'?page='.$posts->currentPage()
            : $baseUrl;

        $hreflangUrls = [$locale => $baseUrl];
        if ($alternateCategoryTranslation) {
            $hreflangUrls[$alternateLocale] = locale_route('categories.show', ['slug' => $alternateCategoryTranslation->slug], $alternateLocale);
        }

        return view('categories.show', compact('category', 'categoryTranslation', 'posts', 'alternateUrls', 'locale', 'canonicalUrl', 'hreflangUrls'));
    }
}
