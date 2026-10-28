<?php

namespace App\Services;

use App\Markdown\ImageRenderer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownService
{
    private readonly MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);

        // Override default image renderer to support YouTube embeds and lazy loading.
        $environment->addRenderer(Image::class, new ImageRenderer, 10);

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * Code blocks are focusable so keyboard users can scroll long lines horizontally (WCAG 2.1.1).
     * CommonMark always emits a bare `<pre>` and raw HTML input is stripped, so the replacement is safe.
     */
    public function toHtml(string $markdown): string
    {
        $html = $this->converter->convert($markdown)->getContent();

        return str_replace('<pre>', '<pre tabindex="0">', $html);
    }
}
