<?php

namespace App\Services;

class FacetNormalizer
{
    protected string $mode;
    protected array  $keys;

    public function __construct()
    {
        $cfg        = config('facets', []);
        $this->mode = $cfg['mode'] ?? 'allow_all';
        // index key configs case-insensitively
        $this->keys = [];
        foreach (($cfg['keys'] ?? []) as $k => $v) {
            $this->keys[mb_strtolower(trim($k))] = $v;
        }
    }

    protected function cfgFor(string $key): ?array
    {
        return $this->keys[mb_strtolower(trim($key))] ?? null;
    }

    /** Should this attribute key be offered as a filter at all? */
    public function isFacet(string $key): bool
    {
        if (trim($key) === '' || mb_strtolower($key) === 'tag_options') {
            return false;
        }

        $cfg = $this->cfgFor($key);

        if ($cfg && !empty($cfg['drop'])) {
            return false;
        }

        if ($this->mode === 'whitelist') {
            return $cfg !== null; // only configured keys
        }

        return true; // allow_all
    }

    /** Human-readable sidebar label. */
    public function label(string $key): string
    {
        $cfg = $this->cfgFor($key);
        if ($cfg && !empty($cfg['label'])) {
            return $cfg['label'];
        }
        // "refresh_rate" / "refresh rate" → "Refresh Rate"
        $clean = str_replace('_', ' ', trim($key));
        return ucwords(mb_strtolower($clean));
    }

    /**
     * Raw stored value → canonical facet value.
     * Returns null when the value should be excluded from faceting.
     */
    public function canonical(string $key, string $raw): ?string
    {
        $raw = trim($raw);
        if ($raw === '') return null;

        $cfg = $this->cfgFor($key);

        // Explicit map
        if ($cfg && ($cfg['type'] ?? null) === 'map') {
            foreach ($cfg['map'] as $pattern => $canonical) {
                if (@preg_match($pattern, $raw)) return $canonical;
            }
            return null; // no rule matched → don't invent a facet value
        }

        // Numeric bucket
        if ($cfg && ($cfg['type'] ?? null) === 'bucket') {
            if (!preg_match('/(\d+(?:\.\d+)?)/', $raw, $m)) return null;
            $n = (float) $m[1];
            foreach ($cfg['buckets'] as $b) {
                $okMin = !isset($b['min']) || $n >= $b['min'];
                $okMax = !isset($b['max']) || $n <= $b['max'];
                if ($okMin && $okMax) return $b['label'];
            }
            return null;
        }

        // Default: light clean (allow_all keys with no specific config)
        return $this->lightClean($raw);
    }

    /**
     * Trim common hand-entry noise so near-identical values collapse:
     *   "144Hz Hardware Base"            → "144Hz Hardware Base" (unchanged text,
     *                                       but parenthetical noise stripped)
     *   "QLED (Quantum Dot LED) / VA"    → "QLED / VA"
     *   "4000:1 (Panel Variance)"        → "4000:1"
     * Drops values that are clearly free-text (too long).
     */
    protected function lightClean(string $raw): ?string
    {
        // strip parenthetical asides:  "X (something)" → "X"
        $v = preg_replace('/\s*\([^)]*\)/', '', $raw);
        $v = trim($v ?? $raw);

        // collapse whitespace
        $v = preg_replace('/\s+/', ' ', $v);

        if ($v === '') return null;

        // free-text guard: very long values aren't facets
        if (mb_strlen($v) > 40) return null;

        return $v;
    }
}