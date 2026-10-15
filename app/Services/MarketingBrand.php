<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class MarketingBrand
{
    public static function current(): array
    {
        $settings = Setting::pluck('value', 'key')->all();
        $logo = $settings['store_logo'] ?? null;
        $logoUrl = $logo ? (str_starts_with($logo, 'https://') ? $logo : Storage::disk('public')->url($logo)) : asset('image.png');
        $phone = self::internationalPhone($settings['store_phone'] ?? '', $settings['store_phone_country_code'] ?? '+234');
        $email = trim($settings['store_email'] ?? '');

        return ['name' => $settings['store_name'] ?? 'AlbertinaNG', 'logo' => $logoUrl, 'address' => $settings['store_address'] ?? '', 'phone' => $phone, 'phone_link' => preg_match('/^\+[1-9][0-9]{6,14}$/', $phone) ? $phone : null, 'email' => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null];
    }

    public static function internationalPhone(?string $phone, string $countryCode = '+234'): string
    {
        $phone = trim($phone ?? '');
        if ($phone === '') {
            return '';
        }
        // Do not merge multiple saved contact numbers into an invalid telephone link.
        if (preg_match('~[,;/]|\bor\b~i', $phone)) {
            return $phone;
        }
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '+')) {
            return '+'.$digits;
        }
        if (str_starts_with($digits, '00')) {
            return '+'.substr($digits, 2);
        }
        $code = preg_replace('/\D/', '', preg_match('/^\+[1-9][0-9]{0,3}$/', $countryCode) ? $countryCode : '+234');
        if (str_starts_with($digits, '0')) {
            return '+'.$code.ltrim($digits, '0');
        }
        if (str_starts_with($digits, $code)) {
            return '+'.$digits;
        }

        return '+'.$code.$digits;
    }
}
