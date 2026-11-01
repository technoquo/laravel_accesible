<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\WcagGuide;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WcagGuideController extends Controller
{
    public function __construct(private WcagGuide $guide) {}

    public function index(Request $request): View
    {
        $level = in_array($request->query('level'), WcagGuide::LEVELS, true)
            ? $request->query('level')
            : null;
        $principle = in_array($request->query('principle'), $this->guide->principles(), true)
            ? $request->query('principle')
            : null;
        $onlyGuides = $request->boolean('guides');

        $criteria = $this->guide->filter($level, $principle, $onlyGuides);
        $groupedCriteria = $this->guide->groupByPrinciple($criteria);
        $principles = $this->guide->principles();
        $isFiltered = $level !== null || $principle !== null || $onlyGuides;

        $canonicalUrl = locale_route('wcag.index');
        $hreflangUrls = [
            'es' => locale_route('wcag.index', [], 'es'),
            'en' => locale_route('wcag.index', [], 'en'),
        ];
        $alternateLocale = app()->getLocale() === 'es' ? 'en' : 'es';
        $alternateUrls = [$alternateLocale => $hreflangUrls[$alternateLocale]];

        return view('wcag.index', compact(
            'criteria', 'groupedCriteria', 'principles', 'level', 'principle', 'onlyGuides', 'isFiltered',
            'canonicalUrl', 'hreflangUrls', 'alternateUrls',
        ));
    }

    public function show(string $locale, string $criterion): View
    {
        $wcagCriterion = $this->guide->findBySlug($criterion);

        abort_if($wcagCriterion === null || ! $wcagCriterion['has_guide'], 404);

        $html = $this->guide->guideHtml($wcagCriterion['number'], $locale);
        $adjacentGuides = $this->guide->adjacentGuides($wcagCriterion);

        $relatedPosts = Post::publishedForLocale($locale)
            ->whereJsonContains('wcag_criteria', $wcagCriterion['number'])
            ->with(['translations' => fn ($q) => $q->where('locale', $locale)
                ->select('id', 'post_id', 'locale', 'title', 'slug', 'excerpt')])
            ->select('id', 'published_at')
            ->latest('published_at')
            ->get();

        $canonicalUrl = locale_route('wcag.show', ['criterion' => $criterion]);
        $hreflangUrls = [
            'es' => locale_route('wcag.show', ['criterion' => $criterion], 'es'),
            'en' => locale_route('wcag.show', ['criterion' => $criterion], 'en'),
        ];
        $alternateLocale = $locale === 'es' ? 'en' : 'es';
        $alternateUrls = [$alternateLocale => $hreflangUrls[$alternateLocale]];

        return view('wcag.show', compact(
            'wcagCriterion', 'html', 'adjacentGuides', 'relatedPosts', 'locale',
            'canonicalUrl', 'hreflangUrls', 'alternateUrls',
        ));
    }
}
