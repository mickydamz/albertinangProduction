<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    // Keep copied catalogue links inside their staging site, preserving the path.
    public function getLinkAttribute($value)
    {
        $host = request()->getHost();
        if (in_array($host, ['test.albertinang.com', 'testing.albertinang.com'], true)
            && in_array(strtolower(parse_url($value ?? '', PHP_URL_HOST) ?? ''), ['albertinang.com', 'www.albertinang.com'], true)) {
            return request()->getSchemeAndHttpHost()
                . (parse_url($value, PHP_URL_PATH) ?: '/')
                . (($query = parse_url($value, PHP_URL_QUERY)) ? '?' . $query : '')
                . (($fragment = parse_url($value, PHP_URL_FRAGMENT)) ? '#' . $fragment : '');
        }
        return $value;
    }

    protected $fillable = [
        'type',  // Banner type (banner1, banner2, popup, etc.)
        'image', // Image path
        'title', // Banner title
         'link',
         'sort_order',
        'status' // Banner status (active or inactive)
    ];
}
