<?php

namespace App\Markdown;

use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

class ImageRenderer implements NodeRendererInterface
{
    /** @var list<string> */
    private const YOUTUBE_PATTERNS = [
        '/^https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([A-Za-z0-9_-]{11})/i',
        '/^https?:\/\/(?:www\.)?youtube\.com\/watch\?.*&v=([A-Za-z0-9_-]{11})/i',
        '/^https?:\/\/youtu\.be\/([A-Za-z0-9_-]{11})/i',
        '/^https?:\/\/(?:www\.)?youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i',
    ];

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): \Stringable|string|null
    {
        Image::assertInstanceOf($node);

        /** @var Image $node */
        $url = $node->getUrl();
        $alt = strip_tags((string) $childRenderer->renderNodes($node->children()));

        foreach (self::YOUTUBE_PATTERNS as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $this->renderYouTube($matches[1], $alt !== '' ? $alt : 'YouTube video');
            }
        }

        return $this->renderImage($url, $alt, $node->getTitle() ?? '');
    }

    private function renderImage(string $url, string $alt, string $title): string
    {
        $attrs = 'src="'.htmlspecialchars($url, ENT_QUOTES | ENT_HTML5, 'UTF-8').'"'
            .' alt="'.htmlspecialchars($alt, ENT_QUOTES | ENT_HTML5, 'UTF-8').'"'
            .' loading="lazy"'
            .' class="article-image"';

        if ($title !== '') {
            $attrs .= ' title="'.htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8').'"';
        }

        return '<img '.$attrs.'>';
    }

    private function renderYouTube(string $videoId, string $title): string
    {
        $id = htmlspecialchars($videoId, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $t = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return '<div class="video-embed" role="figure" aria-label="'.$t.'">'
            .'<iframe'
            .' src="https://www.youtube-nocookie.com/embed/'.$id.'"'
            .' title="'.$t.'"'
            .' allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"'
            .' allowfullscreen'
            .' loading="lazy"'
            .' referrerpolicy="strict-origin-when-cross-origin"'
            .'></iframe>'
            .'</div>';
    }
}
