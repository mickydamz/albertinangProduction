<?php

namespace App\Services;

/** Rebuild rich text from a small allowlist. Never trust editor HTML or pasted styles. */
class MarketingHtml
{
    public static function clean(?string $html): string
    {
        if (! $html) {
            return '';
        }
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $prior = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>', LIBXML_NONET | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prior);
        $render = function ($node) use (&$render): string {
            if ($node instanceof \DOMText) {
                return htmlspecialchars($node->nodeValue, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
            if (! ($node instanceof \DOMElement)) {
                return '';
            }
            $tag = strtolower($node->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'form', 'input', 'button', 'textarea', 'select', 'template', 'noscript'])) {
                return '';
            }
            $children = '';
            foreach ($node->childNodes as $child) {
                $children .= $render($child);
            }
            if (! in_array($tag, ['div', 'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'h2', 'h3', 'ul', 'ol', 'li', 'blockquote', 'a', 'span', 'font'])) {
                return $children;
            }
            if ($tag === 'font') {
                $tag = 'span';
            }
            $attrs = '';
            if ($tag === 'a') {
                $url = trim($node->getAttribute('href'));
                if (filter_var($url, FILTER_VALIDATE_URL) && preg_match('~^https://~i', $url)) {
                    $attrs .= ' href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" rel="noopener noreferrer"';
                }
            }
            $styles = [];
            foreach (explode(';', $node->getAttribute('style')) as $style) {
                $pair = explode(':', $style, 2);
                if (count($pair) !== 2) {
                    continue;
                }
                $key = strtolower(trim($pair[0]));
                $value = strtolower(trim($pair[1]));
                if (($key === 'color' && preg_match('/^(#[0-9a-f]{3}|#[0-9a-f]{6}|rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\))$/', $value)) || ($key === 'text-align' && in_array($value, ['left', 'center', 'right'])) || ($key === 'font-weight' && in_array($value, ['bold', 'normal'])) || ($key === 'font-style' && $value === 'italic') || ($key === 'text-decoration' && in_array($value, ['underline', 'line-through']))) {
                    $styles[$key] = $value;
                }
            }
            $color = $node->getAttribute('color');
            if (preg_match('/^#[0-9a-f]{6}$/i', $color)) {
                $styles['color'] = $color;
            }
            if ($styles) {
                $attrs .= ' style="'.htmlspecialchars(implode(';', array_map(fn ($k, $v) => $k.':'.$v, array_keys($styles), $styles)), ENT_QUOTES, 'UTF-8').'"';
            }

            return $tag === 'br' ? '<br>' : '<'.$tag.$attrs.'>'.$children.'</'.$tag.'>';
        };
        $result = '';
        foreach ($doc->childNodes as $node) {
            $result .= $render($node);
        }

        return $result;
    }

    public static function text(string $html): string
    {
        return trim(html_entity_decode(strip_tags(preg_replace('~</(?:p|div|h2|h3|li|blockquote)>|<br\s*/?>~i', "\n", $html)), ENT_QUOTES, 'UTF-8'));
    }
}
