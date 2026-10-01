<?php

// app/Http/Controllers/AffiliateController.php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    // Constructor to ensure only affiliate users can access
    // public function __construct()
    // {
    //     $this->middleware('role:affiliate'); // Use a middleware to restrict access to affiliates only
    // }

    // Display the affiliate dashboard
    public function dashboard()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the list of referrals (users referred by this affiliate)
        $referrals = User::where('referred_by', $user->id)->get();
        $referralLink = url('/register?ref=' . $user->affiliate_code);

        // Get the total earnings and clicks for the past 7 days
    $startDate = Carbon::now()->subWeek(); // 7 days ago

    // Initialize an array to store metrics for the last 7 days
    $metrics = [];

    // Loop through the last 7 days (including today)
    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->toDateString(); // Get the date for each of the last 7 days
        
        // Retrieve the user data for that specific date (assuming 'updated_at' is when the earnings and clicks were last updated)
        $userDataForDay = User::where('id', $user->id)
            ->whereDate('updated_at', $date) // Filter by the updated_at date
            ->first();

        // Store the data if available, otherwise, set to 0
        $metrics[] = [
            'date' => $date,
            'clicks' => $userDataForDay ? $userDataForDay->total_clicks : 0,
            'total_earnings' => $userDataForDay ? $userDataForDay->total_earnings : 0,
        ];
    }

    // Pass the data to the view
    return view('affiliate.dashboard', compact('user', 'referrals', 'referralLink', 'metrics'));

    }

    // Generate referral link for the affiliate
    public function generateReferralLink()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Generate a referral link for the user
        $referralLink = url('/register?ref=' . $user->affiliate_code);

        return view('affiliate.referral-link', compact('referralLink'));
    }

    // Display the affiliate earnings (optional, based on your business logic)
    public function earnings()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Here, you'd normally calculate the affiliate's earnings based on their referrals
        // For example, let’s assume each successful referral earns $10:
        $earnings = User::where('referred_by', $user->id)->count() * 10; // Example logic

        return view('affiliate.earnings', compact('earnings'));
    }
}
