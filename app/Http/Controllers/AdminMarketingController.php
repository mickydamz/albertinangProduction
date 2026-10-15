<?php

namespace App\Http\Controllers;

use App\Mail\MarketingPromotion;
use App\Models\MarketingCampaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminMarketingController extends Controller
{
    public function index()
    {
        return view('admin.marketing.index', [
            'campaigns' => MarketingCampaign::withCount('deliveries')->latest()->paginate(20),
            'audience' => User::where('role', 'user')->whereNotNull('marketing_subscribed_at')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['subject' => 'required|string|max:150', 'message' => 'required|string|max:20000', 'offer_url' => 'nullable|url|max:2048|regex:~^https://~i']);
        $campaign = MarketingCampaign::create($data + ['created_by' => $request->user()->id]);

        return redirect()->route('admin.marketing.show', $campaign);
    }

    public function update(MarketingCampaign $campaign, Request $request)
    {
        $data = $request->validate(['subject' => 'required|string|max:150', 'message' => 'required|string|max:20000', 'offer_url' => 'nullable|url|max:2048|regex:~^https://~i']);
        DB::transaction(function () use ($campaign, $data) {
            $locked = MarketingCampaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'draft', 409, 'Queued campaigns cannot be edited.');
            $locked->update($data);
        });

        return back()->with('success', 'Draft updated.');
    }

    public function show(MarketingCampaign $campaign)
    {
        return view('admin.marketing.show', ['campaign' => $campaign, 'counts' => $campaign->deliveries()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')]);
    }

    public function preview(MarketingCampaign $campaign, Request $request)
    {
        $email = new MarketingPromotion($campaign, $request->user());

        return view('marketing.email', ['campaign' => $campaign, 'unsubscribeUrl' => $email->unsubscribeUrl]);
    }

    public function test(MarketingCampaign $campaign, Request $request)
    {
        abort_unless(config('marketing.enabled'), 422, 'Marketing email sending is disabled in this environment.');
        // Tests go only to the signed-in administrator, never an arbitrary address.
        Mail::to($request->user()->email)->send(new MarketingPromotion($campaign, $request->user()));

        return back()->with('success', 'Test email accepted by the mail transport for your admin address.');
    }

    public function send(MarketingCampaign $campaign, Request $request)
    {
        abort_unless(config('marketing.enabled'), 422, 'Marketing email sending is disabled in this environment.');
        $request->validate(['confirm' => 'accepted']);
        DB::transaction(function () use ($campaign) {
            $locked = MarketingCampaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'draft', 409, 'This campaign has already been queued.');
            User::where('role', 'user')->whereNotNull('marketing_subscribed_at')->select(['id', 'email'])->chunkById(500, function ($users) use ($locked) {
                $rows = $users->map(fn ($user) => ['marketing_campaign_id' => $locked->id, 'user_id' => $user->id, 'email' => $user->email, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()])->all();
                DB::table('marketing_deliveries')->insert($rows);
            });
            $locked->status = $locked->deliveries()->exists() ? 'queued' : 'completed';
            $locked->save();
        });

        return back()->with('success', 'Campaign queued. Delivery progress appears below.');
    }
}
