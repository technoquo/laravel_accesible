<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostTranslation;
use App\PostStatus;
use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $search = (string) $request->string('search')->trim()->substr(0, 100);

        $query = Post::publishedForLocale($locale)
            ->with([
                'translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'post_id', 'locale', 'title', 'slug', 'excerpt'),
                'category.translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'category_id', 'locale', 'name', 'slug'),
                'tags.translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'tag_id', 'locale', 'name', 'slug'),
            ])
            ->select('id', 'category_id', 'published_at')
            ->latest('published_at');

        if ($search !== '') {
            $query->whereHas('translations', fn ($q) => $q
                ->where('locale', $locale)
                ->where(fn ($q) => $q
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhere('excerpt', 'like', '%'.$search.'%')
                )
            );
        }

        $posts = $query->paginate(10)->withQueryString();

        $baseUrl = locale_route('articles.index');
        $canonicalUrl = $posts->currentPage() > 1
            ? $baseUrl.'?page='.$posts->currentPage()
            : $baseUrl;

        $hreflangUrls = [
            'es' => locale_route('articles.index', [], 'es'),
            'en' => locale_route('articles.index', [], 'en'),
        ];

        return view('articles.index', compact('posts', 'search', 'locale', 'canonicalUrl', 'hreflangUrls'));
    }

    public function show(string $locale, string $slug): View
    {
        $translation = PostTranslation::where('locale', $locale)
            ->where('slug', $slug)
            ->firstOrFail();

        $post = Post::with([
            'translations:id,post_id,locale,title,slug,excerpt,meta_title,meta_description',
            'category.translations' => fn ($q) => $q->where('locale', $locale)
                ->select('id', 'category_id', 'locale', 'name', 'slug'),
            'tags.translations' => fn ($q) => $q->where('locale', $locale)
                ->select('id', 'tag_id', 'locale', 'name', 'slug'),
            'author:id,name',
        ])->findOrFail($translation->post_id);

        abort_if(
            $post->status !== PostStatus::Published || $post->published_at > now(),
            404
        );

        $html = app(MarkdownService::class)->toHtml($translation->markdown_content ?? '');

        $alternateLocale = $locale === 'es' ? 'en' : 'es';
        $alternateTranslation = $post->translations->firstWhere('locale', $alternateLocale);
        $alternateUrls = [
            $alternateLocale => $alternateTranslation
                ? locale_route('articles.show', ['slug' => $alternateTranslation->slug], $alternateLocale)
                : locale_route('articles.index', [], $alternateLocale),
        ];

        $canonicalUrl = locale_route('articles.show', ['slug' => $slug]);
        $hreflangUrls = [$locale => $canonicalUrl];
        if ($alternateTranslation) {
            $hreflangUrls[$alternateLocale] = locale_route('articles.show', ['slug' => $alternateTranslation->slug], $alternateLocale);
        }

        return view('articles.show', compact('post', 'translation', 'html', 'alternateUrls', 'locale', 'canonicalUrl', 'hreflangUrls'));
    }
}
