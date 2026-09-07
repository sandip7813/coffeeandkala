<?php

namespace App\Support;

/**
 * Strips presentational markup (inline style/class/id/color/background/font
 * attributes, and <font>/<style> tags) from admin-authored rich text before
 * it reaches the frontend — Quill (and anything pasted into it from Word,
 * Google Docs, etc.) can carry inline styling that would otherwise fight
 * the site's own typography (wrong font, a stray white background, broken
 * alignment). Structural formatting — bold/italic/headings/lists/links —
 * is untouched; only how it *looks* is left entirely to the frontend's CSS.
 */
class HtmlSanitizer
{
    private const PRESENTATIONAL_ATTRIBUTES = [
        'style', 'class', 'id', 'color', 'bgcolor', 'background',
        'align', 'face', 'size', 'width', 'height',
    ];

    public static function stripPresentationalMarkup(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $dom = new \DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="utf-8"?><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $wrapper = $dom->getElementsByTagName('div')->item(0);

        if ($wrapper === null) {
            return $html;
        }

        // <style>/<font> tags are purely presentational — <style> is
        // removed outright, <font> is unwrapped (its content kept, the tag
        // itself dropped) below via the same attribute-stripping pass,
        // since <font> carries no structural meaning once its attributes
        // (color/face/size) are gone.
        foreach (iterator_to_array($dom->getElementsByTagName('style')) as $styleTag) {
            $styleTag->parentNode?->removeChild($styleTag);
        }

        $xpath = new \DOMXPath($dom);
        $conditions = implode(' or ', array_map(fn (string $attribute): string => "@{$attribute}", self::PRESENTATIONAL_ATTRIBUTES));

        foreach ($xpath->query("//*[{$conditions}]") as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }

            foreach (self::PRESENTATIONAL_ATTRIBUTES as $attribute) {
                $node->removeAttribute($attribute);
            }
        }

        $result = '';
        foreach ($wrapper->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return $result;
    }
}
