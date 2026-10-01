<?php

namespace App\Http\Controllers;

use App\Models\StoreLocation;

class StoreLocationController extends Controller
{
    public function index()
    {
        $locations = StoreLocation::orderByDesc('is_hq')->orderBy('sort_order')->orderBy('name')->get();
        return view('sims.store_locator', compact('locations'));
    }
}
