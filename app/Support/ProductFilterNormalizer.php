<?php
namespace App\Support;

/** A read-only projection: product descriptions and stored specifications are unchanged. */
class ProductFilterNormalizer
{
    public static function key(string $key): string
    {
        $key = strtolower(trim(preg_replace('/[\s_\/\-]+/u', ' ', $key)));
        return [
            'display resolution'=>'resolution','picture resolution'=>'resolution',
            'refresh rate'=>'refresh_rate','native refresh rate'=>'refresh_rate',
            'frequency'=>'frequency','usb ports'=>'usb_ports','usb port'=>'usb_ports','usb'=>'usb_ports',
            'hdmi ports'=>'hdmi_ports','hdmi port'=>'hdmi_ports',
            'color'=>'colour','colors'=>'colour','colour'=>'colour','colours'=>'colour',
            'color finish'=>'colour','colour finish'=>'colour',
            'screen size'=>'screen_size','display size'=>'screen_size',
            'refrigerant'=>'refrigerant_gas','refrigerant type'=>'refrigerant_gas',
            'type'=>'configuration',
            'load capacity'=>'load_capacity','capacity btu'=>'capacity_btu',
        ][$key] ?? str_replace(' ', '_', $key);
    }

    public static function value(string $key, string $value): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));
        $key = self::key($key);
        if ($key === 'resolution') {
            if (preg_match('/\b8k\b|7680\s*[x×]\s*4320|4320p/i', $value)) return '8K Ultra HD';
            if (preg_match('/\b4k\b|\buhd\b|ultra hd|3840\s*[x×]\s*2160|2160p/i', $value)) return '4K Ultra HD';
            if (preg_match('/full hd|\bfhd\b|1920\s*[x×]\s*1080|1080p/i', $value)) return 'Full HD (1080p)';
            if (preg_match('/hd ready|1366\s*[x×]\s*768|720p/i', $value)) return 'HD Ready (720p)';
        }
        if (in_array($key, ['frequency','refresh_rate'], true) && preg_match('/^(\d+(?:\.\d+)?)\s*hz(?:\s+(?:native|base architecture))?$/i', $value, $m)) return $m[1].' Hz';
        if (in_array($key, ['usb_ports','hdmi_ports'], true) && preg_match('/^(\d+)\s*(?:\(|x?\s*(?:USB|HDMI)|$)/i', $value, $m)) return $m[1].($m[1] === '1' ? ' port' : ' ports');
        if (in_array($key, ['usb_ports','hdmi_ports'], true) && strcasecmp($value, 'Yes') === 0) return 'Available (count not specified)';
        if ($key === 'screen_size' && preg_match('/^(\d+(?:\.\d+)?)[ -]*(?:["″]|inch(?:es)?|in)(?:\s*\([^)]*\))?$/i', $value, $m)) return $m[1].' inches';
        if (in_array($key, ['load_capacity','capacity','capacity_btu','power_output','blade_size'], true)
            && preg_match('/^(\d+(?:\.\d+)?)\s*(kg|kilograms?|l|litres?|liters?|btu|kva|kw)$/i', $value, $m)) {
            $unit = strtolower($m[2]);
            if (str_starts_with($unit, 'kilo')) $unit = 'kg';
            if ($unit === 'l' || str_starts_with($unit, 'lit')) $unit = 'litres';
            if ($unit === 'btu') $unit = 'BTU';
            if ($unit === 'kva') $unit = 'kVA';
            if ($unit === 'kw') $unit = 'kW';
            return (string) (float) $m[1].' '.$unit;
        }
        if ($key === 'refrigerant_gas' && preg_match('/^(R\d+[A-Z]?)(?:\s*\((?:eco[ -]?friendly|environmentally safe|eco[ -]?friendly\s*\/\s*environmentally safe)\))?$/i', $value, $m)) return ['R600A'=>'R600a','R134A'=>'R134a'][strtoupper($m[1])] ?? strtoupper($m[1]);
        if ($key === 'colour') {
            // Only collapse one unambiguous colour; keep mixed colours and finishes distinct.
            foreach (['Silver','White','Black','Grey','Gray','Red','Blue','Gold'] as $colour) {
                if (preg_match('/^(?:(?:premium |sleek )?metallic |premium |sleek )?'.preg_quote($colour,'/').'(?: finish)?$/i', $value)) return $colour === 'Gray' ? 'Grey' : $colour;
            }
        }
        return $value;
    }

    public static function attributes(array $attributes): array
    {
        $result=[];
        foreach ($attributes as $key=>$values) {
            $key=self::key((string)$key);
            foreach ((array)$values as $value) {
                if (!is_scalar($value)) continue;
                $value=self::value($key,(string)$value);
                if ($value !== '') $result[$key][]=$value;
            }
        }
        foreach ($result as &$values) $values=array_values(array_unique($values));
        return $result;
    }
}
