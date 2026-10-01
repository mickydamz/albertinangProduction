<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAffiliateController extends Controller
{
    // Constructor to ensure only admin can access these routes
    public function __construct()
    {
        $this->middleware('role:admin'); // Ensure only admin can access these functions
    }

    // Show the form for creating a new affiliate
    public function create()
    {
        return view('admin.affiliates.create');
    }

    // Display all affiliates
    public function index()
    {
        // Fetch all affiliates with the count of their referrals
        $affiliates = User::whereNotNull('affiliate_code') // Only users with affiliate codes
            ->withCount('referrals') // Get the number of referrals for each affiliate
            ->get();

        return view('admin.affiliates.index', compact('affiliates'));
    }

    // View a specific affiliate's dashboard
    public function show($id)
    {
        // Fetch the affiliate user
        $affiliate = User::findOrFail($id);

        // Fetch referrals for this affiliate
        $referrals = User::where('referred_by', $affiliate->id)->get();

        return view('admin.affiliates.show', compact('affiliate', 'referrals'));
    }

    // Manage earnings for an affiliate
    public function earnings($id)
    {
        // Fetch the affiliate user
        $affiliate = User::findOrFail($id);

        // Example logic: Calculate earnings based on referrals
        $earnings = User::where('referred_by', $affiliate->id)->count() * 10; // $10 per referral

        return view('admin.affiliates.earnings', compact('affiliate', 'earnings'));
    }

    // Update an affiliate's status (approve/reject or assign/remove affiliate code)
    public function updateAffiliateStatus(Request $request, $id)
    {
        $affiliate = User::findOrFail($id);

        // Example: Toggle affiliate status
        $affiliate->is_approved = !$affiliate->is_approved;
        $affiliate->save();

        return redirect()->route('admin.affiliates.index')->with('status', 'Affiliate status updated successfully!');
    }

    // Show the form for editing an affiliate
    public function edit($id)
    {
        // Fetch the affiliate user to be edited
        $affiliate = User::findOrFail($id);

        // Show the edit form with affiliate data
        return view('admin.affiliates.edit', compact('affiliate'));
    }

    // Store a newly created affiliate
    public function store(Request $request)
    {
        // Validate the incoming data, including password and password confirmation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'affiliate_code' => 'required|string|unique:users,affiliate_code',
            'referred_by' => 'nullable|exists:users,id', // Ensure the referred_by user exists
            'password' => 'required|string|min:8|confirmed', // Ensure password is confirmed
        ]);

        // Create a new affiliate user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'affiliate_code' => $request->affiliate_code,
            'referred_by' => $request->referred_by, // Optionally track who referred this user
            'password' => Hash::make($request->password), // Hash the password
            'is_approved' => false, // Default status could be "pending"
            'total_clicks' => 0,  // Initialize total clicks
            'total_bounties' => 0, // Initialize total bounties
            'total_items_shipped' => 0, // Initialize total items shipped
            'total_earnings' => 0.00, // Initialize total earnings
            'total_orders' => 0, // Initialize total orders
            'clicks' => 0, // Initialize current clicks (could be updated real-time)
            'conversions' => 0, // Initialize conversions
        ]);

        // Assign the 'affiliate' role to the user (if you're using roles)
        $user->assignRole('affiliate'); 

        return redirect()->route('admin.affiliates.index')->with('status', 'Affiliate created successfully!');
    }

    // Update an affiliate's statistics (clicks, bounties, etc.)
    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'total_clicks' => 'nullable|integer',
            'total_bounties' => 'nullable|integer',
            'total_items_shipped' => 'nullable|integer',
            'total_earnings' => 'nullable|numeric',
            'total_orders' => 'nullable|integer',
            'clicks' => 'nullable|integer',
            'conversions' => 'nullable|integer',
        ]);

        // Fetch the affiliate user
        $affiliate = User::findOrFail($id);

        // Update the affiliate fields
        $affiliate->update([
            'total_clicks' => $request->total_clicks ?? $affiliate->total_clicks,
            'total_bounties' => $request->total_bounties ?? $affiliate->total_bounties,
            'total_items_shipped' => $request->total_items_shipped ?? $affiliate->total_items_shipped,
            'total_earnings' => $request->total_earnings ?? $affiliate->total_earnings,
            'total_orders' => $request->total_orders ?? $affiliate->total_orders,
            'clicks' => $request->clicks ?? $affiliate->clicks,
            'conversions' => $request->conversions ?? $affiliate->conversions,
        ]);

        return redirect()->route('admin.affiliates.index')->with('status', 'Affiliate updated successfully!');
    }

    // Track a click for the affiliate (example for tracking real-time clicks)
    public function trackClick($affiliateId)
    {
        // Find the affiliate user
        $affiliate = User::findOrFail($affiliateId);

        // Increment clicks for the affiliate
        $affiliate->increment('clicks');
        $affiliate->increment('total_clicks'); // Update total clicks

        // Optionally, if you want to log this action (e.g., for reporting purposes)
        // You could store click tracking in a separate table for further analysis.

        return response()->json(['message' => 'Click tracked successfully!']);
    }
}
