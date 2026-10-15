<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveMarketingCampaign;
use App\Mail\MarketingPromotion;
use App\Models\MarketingCampaign;
use App\Models\User;
use App\Services\MarketingCampaignContent;
use App\Services\MarketingDispatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AdminMarketingController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketingCampaign::withCount('deliveries')->latest();
        if (in_array($request->status, ['draft', 'scheduled', 'queued', 'paused', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        return view('admin.marketing.index', ['campaigns' => $query->paginate(20)->withQueryString(), 'audience' => User::where('role', 'user')->whereNotNull('marketing_subscribed_at')->count(), 'customers' => User::where('role', 'user')->whereNotNull('marketing_subscribed_at')->orderBy('name')->limit(1000)->get(['id', 'name', 'email'])]);
    }

    public function store(SaveMarketingCampaign $request)
    {
        $campaign = MarketingCampaign::create(MarketingCampaignContent::data($request) + ['created_by' => $request->user()->id]);

        return redirect()->route('admin.marketing.show', $campaign);
    }

    public function update(MarketingCampaign $campaign, SaveMarketingCampaign $request)
    {
        DB::transaction(function () use ($campaign, $request) {
            $locked = MarketingCampaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'draft', 409, 'Queued campaigns cannot be edited.');
            $locked->update(MarketingCampaignContent::data($request, $locked));
        });

        return back()->with('success', 'Draft updated.');
    }

    public function show(MarketingCampaign $campaign)
    {
        return view('admin.marketing.show', [
            'campaign' => $campaign,
            'counts' => $campaign->deliveries()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'clicked' => $campaign->deliveries()->whereNotNull('clicked_at')->count(),
            'unsubscribed' => $campaign->deliveries()->whereNotNull('unsubscribed_at')->count(),
            'deliveries' => $campaign->deliveries()->orderBy('id')->paginate(30),
            'audience' => $campaign->eligibleUsers()->count(),
            'customers' => User::where('role', 'user')->whereNotNull('marketing_subscribed_at')->orderBy('name')->limit(1000)->get(['id', 'name', 'email']),
        ]);
    }

    public function preview(MarketingCampaign $campaign, Request $request)
    {
        $email = new MarketingPromotion($campaign, $request->user());

        return view('marketing.email', $email->viewData());
    }

    public function test(MarketingCampaign $campaign, Request $request)
    {
        abort_unless(config('marketing.test_enabled') || config('marketing.enabled'), 422, 'Marketing test email sending is disabled in this environment.');
        abort_if(in_array(config('mail.mailers.'.config('marketing.mailer').'.transport'), ['log', 'array']), 422, 'Configure a real marketing mail transport before sending a test.');
        try {
            Mail::mailer(config('marketing.mailer'))->to($request->user()->email)->send(new MarketingPromotion($campaign, $request->user(), null, true));
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['mail' => 'The mail server did not confirm this test. Check the marketing sender configuration before retrying.']);
        }
        $campaign->forceFill(['tested_at' => now()])->save();

        return back()->with('success', 'Test email accepted by the mail transport for your admin address. Verify its images, links and sender in your inbox.');
    }

    public function send(MarketingCampaign $campaign, Request $request)
    {
        abort_unless(config('marketing.enabled'), 422, 'Marketing email sending is disabled in this environment.');
        abort_if(in_array(config('mail.mailers.'.config('marketing.mailer').'.transport'), ['log', 'array']), 422, 'A real marketing mail transport is required.');
        $request->validate(['confirm' => 'accepted', 'scheduled_at' => 'nullable|date_format:Y-m-d\\TH:i']);
        $scheduled = null;
        if ($request->filled('scheduled_at')) {
            $scheduled = Carbon::createFromFormat('Y-m-d\\TH:i', $request->scheduled_at, 'Europe/London')->utc();
            if (! $scheduled->isFuture()) {
                throw ValidationException::withMessages(['scheduled_at' => 'Choose a future London date and time.']);
            }
        }
        MarketingDispatch::queue($campaign, $scheduled);

        return back()->with('success', $scheduled ? 'Campaign scheduled.' : 'Campaign queued. Delivery progress appears below.');
    }

    public function duplicate(MarketingCampaign $campaign, Request $request)
    {
        $copy = $campaign->replicate(['status', 'scheduled_at', 'tested_at']);
        $copy->subject = mb_substr('Copy — '.$campaign->subject, 0, 150);
        $copy->status = 'draft';
        $copy->created_by = $request->user()->id;
        $copy->save();

        return redirect()->route('admin.marketing.show', $copy)->with('success', 'Campaign copied to a new draft.');
    }

    public function control(MarketingCampaign $campaign, Request $request)
    {
        $request->validate(['action' => 'required|in:pause,resume,cancel']);
        DB::transaction(function () use ($campaign, $request) {
            $locked = MarketingCampaign::whereKey($campaign->id)->lockForUpdate()->firstOrFail();
            $transitions = ['pause' => ['queued', 'scheduled'], 'resume' => ['paused'], 'cancel' => ['queued', 'scheduled', 'paused']];
            abort_unless(in_array($locked->status, $transitions[$request->action]), 409, 'Campaign status has changed. Refresh this page.');
            $locked->status = ['pause' => 'paused', 'resume' => ($locked->scheduled_at && $locked->scheduled_at->isFuture() ? 'scheduled' : 'queued'), 'cancel' => 'cancelled'][$request->action];
            $locked->save();
            if ($request->action === 'cancel') {
                $locked->deliveries()->where('status', 'pending')->update(['status' => 'cancelled']);
            }
        });

        return back()->with('success', 'Campaign updated. Any email already being sent may still finish.');
    }

    public function export(MarketingCampaign $campaign)
    {
        return response()->streamDownload(function () use ($campaign) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Status', 'Accepted by transport at', 'First offer click at', 'Offer click requests', 'Unsubscribed at']);
            foreach ($campaign->deliveries()->orderBy('id')->cursor() as $d) {
                $email = preg_match('/^[=+@\-\t\r\n]/', $d->email) ? "'".$d->email : $d->email;
                fputcsv($out, [$email, $d->status, $d->sent_at, $d->clicked_at, $d->click_count, $d->unsubscribed_at]);
            } fclose($out);
        }, 'campaign-'.$campaign->id.'-report.csv', ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store']);
    }
}
