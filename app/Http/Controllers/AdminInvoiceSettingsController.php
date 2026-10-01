<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminInvoiceSettingsController extends Controller
{
    /**
     * Default header element positions (percent of the header zone) and the
     * default top-to-bottom order of body sections. Used when nothing has been
     * saved yet, and to backfill any missing key in a saved layout.
     */
    public const DEFAULT_HDR = [
        'logo'      => ['t' => 5,  'l' => 0,  'w' => 30],
        'storename' => ['t' => 48, 'l' => 0,  'w' => 38],
        'invtitle'  => ['t' => 4,  'l' => 60, 'w' => 40],
        'issuedate' => ['t' => 60, 'l' => 60, 'w' => 40],
    ];

    public const DEFAULT_BODY = ['bill_to', 'fulfillment', 'items', 'notes_totals', 'bank', 'terms', 'footer'];

    /** Valid body section keys — anything else in a saved layout is ignored. */
    public const BODY_SECTIONS = ['bill_to', 'fulfillment', 'items', 'notes_totals', 'bank', 'terms', 'footer'];

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $settings     = Setting::pluck('value', 'key');
        $defaultTerms  = self::defaultTerms();
        $defaultFooter = self::defaultFooter();

        return view('admin.invoice-settings.index', compact('settings', 'defaultTerms', 'defaultFooter'));
    }

    /** The Terms & Conditions block printed when the admin hasn't set custom terms. */
    public static function defaultTerms(): string
    {
        $store   = Setting::get('store_name', config('app.name'));
        $contact = Setting::get('store_email') ?: Setting::get('store_phone') ?: '';

        return "1. This invoice is evidence of your order and payment record. Please retain it for your records.\n"
             . "2. Goods may be returned or exchanged only in accordance with the {$store} Returns Policy; eligibility depends on product condition and category.\n"
             . "3. Report delivery issues or damaged goods within 48 hours of receipt"
             . ($contact ? " via {$contact}" : '') . ", quoting your order number.\n"
             . "4. Prices are in Nigerian Naira (₦).\n"
             . "5. This invoice is generated electronically and requires no signature.";
    }

    /** The footer line printed when the admin hasn't set a custom footer. */
    public static function defaultFooter(): string
    {
        $store   = Setting::get('store_name', config('app.name'));
        $contact = Setting::get('store_email') ?: Setting::get('store_phone') ?: '';

        return "Thank you for shopping with {$store}." . ($contact ? " | {$contact}" : '');
    }

    public function update(Request $request)
    {
        $request->validate([
            'inv_accent_color'    => 'nullable|string|max:20',
            'inv_accent_bg'       => 'nullable|string|max:20',
            'inv_terms'           => 'nullable|string|max:5000',
            'inv_footer'          => 'nullable|string|max:500',
            // One layout per fulfilment mode (JSON: {hdr:{…}, body:[…]}).
            'inv_layout_pickup'   => 'nullable|string|max:5000',
            'inv_layout_delivery' => 'nullable|string|max:5000',
        ]);

        Setting::set('inv_accent_color', $request->filled('inv_accent_color') ? $request->inv_accent_color : '#1a7a4a');
        Setting::set('inv_accent_bg',    $request->filled('inv_accent_bg')    ? $request->inv_accent_bg    : '#e8f5ee');
        Setting::set('inv_terms',        $request->input('inv_terms', ''));
        Setting::set('inv_footer',       $request->input('inv_footer', ''));
        // Header note & bank details were removed from the invoice — keep them cleared.
        Setting::set('inv_header_note',  '');
        Setting::set('inv_bank_details', '');
        Setting::set('inv_layout_pickup',   $request->input('inv_layout_pickup', ''));
        Setting::set('inv_layout_delivery', $request->input('inv_layout_delivery', ''));
        Setting::clearCache();

        return back()->with('success', 'Invoice template saved successfully!');
    }

    /** AJAX layout autosave — accepts either/both per-mode layouts. */
    public function updateLayout(Request $request)
    {
        $request->validate([
            'inv_layout_pickup'   => 'nullable|string|max:5000',
            'inv_layout_delivery' => 'nullable|string|max:5000',
        ]);

        if ($request->filled('inv_layout_pickup')) {
            Setting::set('inv_layout_pickup', $request->input('inv_layout_pickup'));
        }
        if ($request->filled('inv_layout_delivery')) {
            Setting::set('inv_layout_delivery', $request->input('inv_layout_delivery'));
        }
        Setting::clearCache();

        return response()->json(['ok' => true]);
    }

    /**
     * Sanitise a decoded layout array to a safe {hdr, body} shape, backfilling
     * defaults for anything missing or invalid.
     */
    private static function normaliseLayout($layout): array
    {
        $layout = is_array($layout) ? $layout : [];

        // Header: keep only known keys, each with numeric t/l/w.
        $hdr = [];
        foreach (self::DEFAULT_HDR as $key => $default) {
            $pos = $layout['hdr'][$key] ?? [];
            $hdr[$key] = [
                't' => is_numeric($pos['t'] ?? null) ? (float) $pos['t'] : $default['t'],
                'l' => is_numeric($pos['l'] ?? null) ? (float) $pos['l'] : $default['l'],
                'w' => is_numeric($pos['w'] ?? null) ? (float) $pos['w'] : $default['w'],
            ];
        }

        // Body: keep only recognised section keys, in the saved order, then
        // append any sections that were missing so nothing silently disappears.
        $body = [];
        foreach ((array) ($layout['body'] ?? []) as $sec) {
            if (in_array($sec, self::BODY_SECTIONS, true) && !in_array($sec, $body, true)) {
                $body[] = $sec;
            }
        }
        foreach (self::DEFAULT_BODY as $sec) {
            if (!in_array($sec, $body, true)) {
                $body[] = $sec;
            }
        }

        return ['hdr' => $hdr, 'body' => $body];
    }

    /**
     * Resolve the saved layout for a fulfilment mode, falling back to the legacy
     * single `inv_layout`, then to defaults.
     */
    private static function resolveLayout(string $mode): array
    {
        $key  = $mode === 'pickup' ? 'inv_layout_pickup' : 'inv_layout_delivery';
        $json = Setting::get($key, '');
        $data = $json ? (json_decode($json, true) ?: []) : [];

        if (empty($data)) {
            // Migrate transparently from the old single shared layout.
            $legacy = Setting::get('inv_layout', '');
            $data   = $legacy ? (json_decode($legacy, true) ?: []) : [];
        }

        return self::normaliseLayout($data);
    }

    /**
     * Resolve and return template variables for use in invoice views.
     *
     * @param  string|null $mode  Order fulfilment method ('pickup' → pickup
     *         layout, anything else → delivery layout).
     */
    public static function templateVars(?string $mode = null): array
    {
        $mode = $mode === 'pickup' ? 'pickup' : 'delivery';

        return [
            'invAccent'      => Setting::get('inv_accent_color', '#1a7a4a'),
            'invAccentBg'    => Setting::get('inv_accent_bg',    '#e8f5ee'),
            'invHeaderNote'  => Setting::get('inv_header_note',  ''),
            'invTerms'       => Setting::get('inv_terms',        ''),
            'invFooter'      => Setting::get('inv_footer',       ''),
            'invBankDetails' => Setting::get('inv_bank_details', ''),
            'invLayout'      => self::resolveLayout($mode),
            'invMode'        => $mode,
        ];
    }
}
