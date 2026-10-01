<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code', 'symbol', 'name', 'rate_to_ngn',
        'is_base', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_base'     => 'boolean',
        'is_active'   => 'boolean',
        'rate_to_ngn' => 'float',
        'sort_order'  => 'integer',
    ];

    /** All active currencies ordered for display */
    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }

    /** Convert an NGN amount into this currency */
    public function convertFromNgn(float $ngn): float
    {
        if ($this->is_base || $this->rate_to_ngn <= 0) return $ngn;
        return $ngn / $this->rate_to_ngn;
    }

    /** Format an NGN amount as a string in this currency */
    public function format(float $ngn): string
    {
        $value = $this->convertFromNgn($ngn);
        return $this->symbol . number_format($value, 2);
    }
}