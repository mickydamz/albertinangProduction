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
        // Use the transparent email variant for the default company logo.
        if ($url === asset('image.png') && is_file(public_path('marketing-logo-transparent.png'))) {
            return $message ? $message->embed(public_path('marketing-logo-transparent.png')) : asset('marketing-logo-transparent.png');
        }
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
        if ($url === asset('image.png')) {
            // Some hosts serve the site beside the Laravel checkout, rather than its public directory.
            foreach ([public_path('image.png'), dirname(base_path()).'/image.png'] as $file) {
                if (is_file($file) && getimagesize($file) !== false) {
                    return $message->embed($file);
                }
            }
        }

        return $url;
    }
}
