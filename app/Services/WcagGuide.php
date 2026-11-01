<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Reads the WCAG 2.2 criteria from config/wcag.php and renders the
 * Markdown guides stored in resources/wcag/{locale}/{slug}.md.
 *
 * Names and summaries are translated to the current locale; translation keys use
 * the slug form ("2-4-3") because dots would be read as nested keys.
 *
 * @phpstan-type Criterion array{number: string, slug: string, name: string, summary: string, level: string, principle: string, guideline: string, guideline_name: string, understanding_url: string, has_guide: bool, axe_rules: list<string>}
 */
class WcagGuide
{
    public const array LEVELS = ['A', 'AA'];

    public function __construct(private MarkdownService $markdown) {}

    /**
     * @return array<string, Criterion>
     */
    public function all(): array
    {
        $principles = config('wcag.principles');
        $criteria = [];

        foreach (config('wcag.criteria') as $number => $data) {
            [$principleNumber, $guidelineNumber] = explode('.', $number);
            $slug = self::slug($number);

            $criteria[$number] = [
                'number' => $number,
                'slug' => $slug,
                'name' => __('wcag.criteria.'.$slug.'.name'),
                'summary' => __('wcag.criteria.'.$slug.'.summary'),
                'level' => $data['level'],
                'principle' => $principles[$principleNumber],
                'guideline' => $principleNumber.'.'.$guidelineNumber,
                'guideline_name' => __('wcag.guidelines.'.$principleNumber.'-'.$guidelineNumber),
                'understanding_url' => sprintf(config('wcag.understanding_url'), $data['understanding']),
                'has_guide' => $data['has_guide'],
                'axe_rules' => $data['axe_rules'],
            ];
        }

        return $criteria;
    }

    /**
     * @return list<string>
     */
    public function principles(): array
    {
        return array_values(config('wcag.principles'));
    }

    /**
     * @return Criterion|null
     */
    public function find(string $number): ?array
    {
        return $this->all()[$number] ?? null;
    }

    /**
     * @return Criterion|null
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->find(str_replace('-', '.', $slug));
    }

    /**
     * @return array<string, Criterion>
     */
    public function filter(?string $level = null, ?string $principle = null, bool $onlyGuides = false): array
    {
        return array_filter($this->all(), fn (array $criterion): bool => ($level === null || $criterion['level'] === $level)
            && ($principle === null || $criterion['principle'] === $principle)
            && (! $onlyGuides || $criterion['has_guide']));
    }

    /**
     * Groups criteria as [principle => [guideline => [number => criterion]]].
     *
     * @param  array<string, Criterion>  $criteria
     * @return array<string, array<string, array<string, Criterion>>>
     */
    public function groupByPrinciple(array $criteria): array
    {
        $grouped = [];

        foreach ($criteria as $number => $criterion) {
            $grouped[$criterion['principle']][$criterion['guideline']][$number] = $criterion;
        }

        return $grouped;
    }

    /**
     * @return array<string, Criterion>
     */
    public function guides(): array
    {
        return $this->filter(onlyGuides: true);
    }

    /**
     * @param  Criterion  $criterion
     * @return array{previous: Criterion|null, next: Criterion|null}
     */
    public function adjacentGuides(array $criterion): array
    {
        $guides = array_values($this->guides());
        $position = array_search($criterion['number'], array_column($guides, 'number'), true);

        return [
            'previous' => $position > 0 ? $guides[$position - 1] : null,
            'next' => $guides[$position + 1] ?? null,
        ];
    }

    public function guidePath(string $number, string $locale): string
    {
        return resource_path('wcag/'.$locale.'/'.self::slug($number).'.md');
    }

    /**
     * Renders a guide once per file version; editing the Markdown file invalidates the cache.
     */
    public function guideHtml(string $number, string $locale): string
    {
        $path = $this->guidePath($number, $locale);

        return Cache::rememberForever(
            'wcag.guide.'.$locale.'.'.self::slug($number).'.'.filemtime($path),
            fn (): string => $this->markdown->toHtml(file_get_contents($path)),
        );
    }

    public static function slug(string $number): string
    {
        return str_replace('.', '-', $number);
    }
}
