<?php

namespace App\Http\Controllers;

use App\Mail\ContactAutoReply;
use App\Mail\ContactMessageReceived;
use App\Rules\Turnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('sims.contact');
    }

    public function store(Request $request)
    {
        // Honeypot: real visitors never see or fill this field.
        // Fake a normal success so bots don't learn they were caught.
        if (filled($request->input('website'))) {
            return redirect()->back()
                ->with('success', 'Your message has been sent! We\'ll get back to you within 24 hours.');
        }

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255',
            'phone'                 => 'nullable|string|max:30',
            'userMessage'           => 'required|string|max:5000',
            'cf-turnstile-response' => ['required', new Turnstile],
        ]);

        // Dedupe: if the exact same message from the same email was
        // submitted in the last 5 minutes, silently no-op (handles
        // double-clicks / page refresh resubmits) but still show success.
        $fingerprint = 'contact-dup:' . md5(strtolower($validated['email']) . $validated['userMessage']);

        if (Cache::has($fingerprint)) {
            return redirect()->back()
                ->with('success', 'Your message has been sent! We\'ll get back to you within 24 hours.');
        }

        Cache::put($fingerprint, true, now()->addMinutes(5));

        // Notify admin
        try {
            Mail::to(config('mail.admin_email', 'Info@Albertinang.com'))
                ->send(new ContactMessageReceived(
                    name:        $validated['name'],
                    email:       $validated['email'],
                    userMessage: $validated['userMessage'],
                    phone:       $validated['phone'] ?? null,
                ));
        } catch (\Exception $e) {
            Log::error('Contact admin email failed: ' . $e->getMessage());
        }

        // Auto-reply to customer
        try {
            Mail::to($validated['email'])
                ->send(new ContactAutoReply(
                    name:        $validated['name'],
                    email:       $validated['email'],
                    userMessage: $validated['userMessage'],
                ));
        } catch (\Exception $e) {
            Log::error('Contact auto-reply failed: ' . $e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Your message has been sent! We\'ll get back to you within 24 hours.');
    }
}