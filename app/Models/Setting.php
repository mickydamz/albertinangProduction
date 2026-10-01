<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    private static array $cache = [];
    private static bool  $loaded = false;

    /** Return all settings as key=>value array, loading once per request. */
    private static function loadAll(): void
    {
        if (!static::$loaded) {
            static::all()->each(fn ($s) => static::$cache[$s->key] = $s->value);
            static::$loaded = true;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        static::loadAll();
        return static::$cache[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache[$key] = $value;
    }

    /** Bust the in-memory cache (call after bulk updates). */
    public static function clearCache(): void
    {
        static::$cache  = [];
        static::$loaded = false;
    }
}
