<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $locale = app()->getLocale();

        $recentPosts = Post::publishedForLocale($locale)
            ->with([
                'translations' => fn ($q) => $q->where('locale', $locale)
                    ->select('id', 'post_id', 'locale', 'title', 'slug', 'excerpt'),
            ])
            ->select('id', 'published_at')
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('home', compact('recentPosts', 'locale'));
    }
}
