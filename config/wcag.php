<?php

/*
|--------------------------------------------------------------------------
| WCAG 2.2 success criteria (levels A and AA)
|--------------------------------------------------------------------------
|
| Metadata for the WCAG guide. Translated names and summaries live in
| lang/{locale}/wcag.php and detailed guides in resources/wcag/{locale}.
| "understanding" is the slug of the W3C "Understanding WCAG 2.2" page and
| "axe_rules" lists the main axe-core rules tagged with the criterion.
|
*/

return [

    'understanding_url' => 'https://www.w3.org/WAI/WCAG22/Understanding/%s.html',

    'principles' => [
        '1' => 'perceivable',
        '2' => 'operable',
        '3' => 'understandable',
        '4' => 'robust',
    ],

    'criteria' => [
        // 1. Perceivable
        '1.1.1' => ['level' => 'A', 'understanding' => 'non-text-content', 'has_guide' => true, 'axe_rules' => ['image-alt', 'input-image-alt', 'object-alt', 'role-img-alt', 'svg-img-alt']],
        '1.2.1' => ['level' => 'A', 'understanding' => 'audio-only-and-video-only-prerecorded', 'has_guide' => false, 'axe_rules' => []],
        '1.2.2' => ['level' => 'A', 'understanding' => 'captions-prerecorded', 'has_guide' => false, 'axe_rules' => ['video-caption']],
        '1.2.3' => ['level' => 'A', 'understanding' => 'audio-description-or-media-alternative-prerecorded', 'has_guide' => false, 'axe_rules' => []],
        '1.2.4' => ['level' => 'AA', 'understanding' => 'captions-live', 'has_guide' => false, 'axe_rules' => []],
        '1.2.5' => ['level' => 'AA', 'understanding' => 'audio-description-prerecorded', 'has_guide' => false, 'axe_rules' => []],
        '1.3.1' => ['level' => 'A', 'understanding' => 'info-and-relationships', 'has_guide' => true, 'axe_rules' => ['definition-list', 'dlitem', 'list', 'listitem', 'td-headers-attr', 'th-has-data-cells']],
        '1.3.2' => ['level' => 'A', 'understanding' => 'meaningful-sequence', 'has_guide' => false, 'axe_rules' => []],
        '1.3.3' => ['level' => 'A', 'understanding' => 'sensory-characteristics', 'has_guide' => false, 'axe_rules' => []],
        '1.3.4' => ['level' => 'AA', 'understanding' => 'orientation', 'has_guide' => false, 'axe_rules' => []],
        '1.3.5' => ['level' => 'AA', 'understanding' => 'identify-input-purpose', 'has_guide' => false, 'axe_rules' => ['autocomplete-valid']],
        '1.4.1' => ['level' => 'A', 'understanding' => 'use-of-color', 'has_guide' => false, 'axe_rules' => ['link-in-text-block']],
        '1.4.2' => ['level' => 'A', 'understanding' => 'audio-control', 'has_guide' => false, 'axe_rules' => ['no-autoplay-audio']],
        '1.4.3' => ['level' => 'AA', 'understanding' => 'contrast-minimum', 'has_guide' => true, 'axe_rules' => ['color-contrast']],
        '1.4.4' => ['level' => 'AA', 'understanding' => 'resize-text', 'has_guide' => false, 'axe_rules' => ['meta-viewport']],
        '1.4.5' => ['level' => 'AA', 'understanding' => 'images-of-text', 'has_guide' => false, 'axe_rules' => []],
        '1.4.10' => ['level' => 'AA', 'understanding' => 'reflow', 'has_guide' => true, 'axe_rules' => []],
        '1.4.11' => ['level' => 'AA', 'understanding' => 'non-text-contrast', 'has_guide' => false, 'axe_rules' => []],
        '1.4.12' => ['level' => 'AA', 'understanding' => 'text-spacing', 'has_guide' => false, 'axe_rules' => ['avoid-inline-spacing']],
        '1.4.13' => ['level' => 'AA', 'understanding' => 'content-on-hover-or-focus', 'has_guide' => false, 'axe_rules' => []],

        // 2. Operable
        '2.1.1' => ['level' => 'A', 'understanding' => 'keyboard', 'has_guide' => true, 'axe_rules' => ['frame-focusable-content', 'scrollable-region-focusable', 'server-side-image-map']],
        '2.1.2' => ['level' => 'A', 'understanding' => 'no-keyboard-trap', 'has_guide' => false, 'axe_rules' => []],
        '2.1.4' => ['level' => 'A', 'understanding' => 'character-key-shortcuts', 'has_guide' => false, 'axe_rules' => []],
        '2.2.1' => ['level' => 'A', 'understanding' => 'timing-adjustable', 'has_guide' => false, 'axe_rules' => ['meta-refresh']],
        '2.2.2' => ['level' => 'A', 'understanding' => 'pause-stop-hide', 'has_guide' => false, 'axe_rules' => ['blink', 'marquee']],
        '2.3.1' => ['level' => 'A', 'understanding' => 'three-flashes-or-below-threshold', 'has_guide' => false, 'axe_rules' => []],
        '2.4.1' => ['level' => 'A', 'understanding' => 'bypass-blocks', 'has_guide' => false, 'axe_rules' => ['bypass']],
        '2.4.2' => ['level' => 'A', 'understanding' => 'page-titled', 'has_guide' => false, 'axe_rules' => ['document-title']],
        '2.4.3' => ['level' => 'A', 'understanding' => 'focus-order', 'has_guide' => true, 'axe_rules' => []],
        '2.4.4' => ['level' => 'A', 'understanding' => 'link-purpose-in-context', 'has_guide' => false, 'axe_rules' => ['area-alt', 'link-name']],
        '2.4.5' => ['level' => 'AA', 'understanding' => 'multiple-ways', 'has_guide' => false, 'axe_rules' => []],
        '2.4.6' => ['level' => 'AA', 'understanding' => 'headings-and-labels', 'has_guide' => false, 'axe_rules' => []],
        '2.4.7' => ['level' => 'AA', 'understanding' => 'focus-visible', 'has_guide' => true, 'axe_rules' => []],
        '2.4.11' => ['level' => 'AA', 'understanding' => 'focus-not-obscured-minimum', 'has_guide' => true, 'axe_rules' => []],
        '2.5.1' => ['level' => 'A', 'understanding' => 'pointer-gestures', 'has_guide' => false, 'axe_rules' => []],
        '2.5.2' => ['level' => 'A', 'understanding' => 'pointer-cancellation', 'has_guide' => false, 'axe_rules' => []],
        '2.5.3' => ['level' => 'A', 'understanding' => 'label-in-name', 'has_guide' => false, 'axe_rules' => ['label-content-name-mismatch']],
        '2.5.4' => ['level' => 'A', 'understanding' => 'motion-actuation', 'has_guide' => false, 'axe_rules' => []],
        '2.5.7' => ['level' => 'AA', 'understanding' => 'dragging-movements', 'has_guide' => false, 'axe_rules' => []],
        '2.5.8' => ['level' => 'AA', 'understanding' => 'target-size-minimum', 'has_guide' => true, 'axe_rules' => ['target-size']],

        // 3. Understandable
        '3.1.1' => ['level' => 'A', 'understanding' => 'language-of-page', 'has_guide' => true, 'axe_rules' => ['html-has-lang', 'html-lang-valid', 'html-xml-lang-mismatch']],
        '3.1.2' => ['level' => 'AA', 'understanding' => 'language-of-parts', 'has_guide' => true, 'axe_rules' => ['valid-lang']],
        '3.2.1' => ['level' => 'A', 'understanding' => 'on-focus', 'has_guide' => false, 'axe_rules' => []],
        '3.2.2' => ['level' => 'A', 'understanding' => 'on-input', 'has_guide' => false, 'axe_rules' => []],
        '3.2.3' => ['level' => 'AA', 'understanding' => 'consistent-navigation', 'has_guide' => false, 'axe_rules' => []],
        '3.2.4' => ['level' => 'AA', 'understanding' => 'consistent-identification', 'has_guide' => false, 'axe_rules' => []],
        '3.2.6' => ['level' => 'A', 'understanding' => 'consistent-help', 'has_guide' => false, 'axe_rules' => []],
        '3.3.1' => ['level' => 'A', 'understanding' => 'error-identification', 'has_guide' => true, 'axe_rules' => []],
        '3.3.2' => ['level' => 'A', 'understanding' => 'labels-or-instructions', 'has_guide' => true, 'axe_rules' => ['form-field-multiple-labels']],
        '3.3.3' => ['level' => 'AA', 'understanding' => 'error-suggestion', 'has_guide' => false, 'axe_rules' => []],
        '3.3.4' => ['level' => 'AA', 'understanding' => 'error-prevention-legal-financial-data', 'has_guide' => false, 'axe_rules' => []],
        '3.3.7' => ['level' => 'A', 'understanding' => 'redundant-entry', 'has_guide' => false, 'axe_rules' => []],
        '3.3.8' => ['level' => 'AA', 'understanding' => 'accessible-authentication-minimum', 'has_guide' => false, 'axe_rules' => []],

        // 4. Robust
        '4.1.2' => ['level' => 'A', 'understanding' => 'name-role-value', 'has_guide' => true, 'axe_rules' => ['aria-allowed-attr', 'aria-hidden-focus', 'aria-input-field-name', 'aria-required-attr', 'aria-roles', 'aria-valid-attr', 'aria-valid-attr-value', 'button-name', 'frame-title', 'label', 'link-name', 'nested-interactive', 'select-name']],
        '4.1.3' => ['level' => 'AA', 'understanding' => 'status-messages', 'has_guide' => true, 'axe_rules' => []],
    ],

];
