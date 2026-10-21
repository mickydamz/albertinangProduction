<?php
namespace App\Support;

final class DeliveryMobile
{
    // Nigerian mobile in local format, or an international number with country code.
    public static function normalize($value): ?string
    {
        if (!is_string($value) || strlen($value) > 40 || !preg_match('/^[+0-9 ()-]+$/D', $value)) return null;
        $number = preg_replace('/[ ()-]/', '', $value);
        if (preg_match('/^0[789][01][0-9]{8}$/D', $number)) $number = '+234'.substr($number, 1);
        if (!preg_match('/^\+[1-9][0-9]{7,14}$/D', $number)) return null;
        if (str_starts_with($number, '+234') && !preg_match('/^\+234[789][01][0-9]{8}$/D', $number)) return null;
        return $number;
    }
}
