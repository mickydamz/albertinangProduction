<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GeoController extends Controller
{
    public function countries(): JsonResponse
    {
        $data = Cache::rememberForever('geo:countries', fn () =>
            Country::orderByRaw("name = 'Nigeria' DESC")
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray()
        );

        return response()->json($data);
    }

    public function statesForCountry(Country $country): JsonResponse
    {
        $data = Cache::rememberForever('geo:states:' . $country->id, fn () =>
            $country->cities()->orderBy('name')->get(['id', 'name'])->toArray()
        );

        return response()->json($data);
    }

    // Legacy name-based endpoint kept for backwards compatibility
    public function states(Request $request): JsonResponse
    {
        $name = trim($request->query('country', ''));
        if (!$name) {
            return response()->json([]);
        }

        $data = Cache::rememberForever('geo:states:name:' . md5($name), function () use ($name) {
            $record = Country::where('name', $name)->first();
            return $record ? $record->cities()->orderBy('name')->get(['id', 'name'])->toArray() : [];
        });

        return response()->json($data);
    }
}
