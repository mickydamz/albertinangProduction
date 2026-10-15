<?php

namespace App\Services;

use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Storage;

class MarketingAssets
{
    public static function picture(string $path, ?Message $message = null): string
    {
        if (! preg_match('~^marketing/[a-zA-Z0-9]+\\.(?:png|jpg|jpeg)$~', $path)) {
            return '';
        }
        $file = Storage::disk('public')->path($path);

        return $message && is_file($file) ? $message->embed($file) : Storage::disk('public')->url($path);
    }

    public static function logo(?string $url, ?Message $message = null): ?string
    {
        if (! $url || ! $message) {
            return $url;
        }
        $storage = rtrim(Storage::disk('public')->url(''), '/').'/';
        if (str_starts_with($url, $storage)) {
            $base = realpath(Storage::disk('public')->path(''));
            $file = realpath(Storage::disk('public')->path(substr($url, strlen($storage))));
            if ($base && $file && str_starts_with($file, $base.DIRECTORY_SEPARATOR) && preg_match('/\\.(png|jpe?g)$/i', $file)) {
                return $message->embed($file);
            }
        }
        if ($url === asset('image.png') && is_file(public_path('image.png'))) {
            return $message->embed(public_path('image.png'));
        }

        return $url;
    }
}
