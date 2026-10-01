<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code'         => 'required|string',
            'subtotal_ngn' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($request->code)))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ], 422);
        }

        $error = $coupon->validate($request->subtotal_ngn, auth()->id());

        if ($error) {
            return response()->json([
                'success' => false,
                'message' => $error,
            ], 422);
        }

        $discountNgn = $coupon->calculateDiscount($request->subtotal_ngn);

        return response()->json([
            'success'       => true,
            'coupon_id'     => $coupon->id,
            'code'          => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'value'         => $coupon->value,
            'discount_ngn'  => $discountNgn,
            'message'       => $coupon->discount_type === 'percent'
                ? number_format($coupon->value, 0) . '% discount applied!'
                : '₦' . number_format($discountNgn, 0) . ' discount applied!',
        ]);
    }
}