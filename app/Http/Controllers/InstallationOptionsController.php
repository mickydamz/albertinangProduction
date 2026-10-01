<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InstallationOptionsController extends Controller
{
    /**
     * Return installation options for a list of product IDs.
     *
     * POST /api/installation-options
     * Body: { "product_ids": [1, 2, 3] }
     *
     * Response:
     * {
     *   "1": [
     *     { "label": "Standard", "price": 0, "description": "..." },
     *     { "label": "Premium",  "price": 5000 }
     *   ],
     *   "2": []
     * }
     */
    public function getForProducts(Request $request)
    {
        $request->validate([
            'product_ids'   => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->product_ids)
            ->select('id', 'installation_options')
            ->get();

        $result = [];

        foreach ($products as $product) {
            $opts = $product->installation_options;

            // Normalise: could be a JSON string, an array, or null
            if (is_string($opts)) {
                $opts = json_decode($opts, true) ?? [];
            }

            $result[$product->id] = is_array($opts) ? $opts : [];
        }

        // Ensure every requested ID appears in the response (even with empty array)
        foreach ($request->product_ids as $id) {
            if (!isset($result[$id])) {
                $result[$id] = [];
            }
        }

        return response()->json($result);
    }
}