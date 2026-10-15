<?php

namespace App\Services;

use App\Http\Requests\SaveMarketingCampaign;
use App\Models\MarketingCampaign;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MarketingCampaignContent
{
    public static function data(SaveMarketingCampaign $request, ?MarketingCampaign $campaign = null): array
    {
        $data = $request->validated();
        $html = MarketingHtml::clean($data['body_html'] ?? null);
        $text = $html ? MarketingHtml::text($html) : trim($data['message'] ?? '');
        if ($text === '') {
            throw ValidationException::withMessages(['body_html' => 'Write a promotion message.']);
        }
        $data['body_html'] = $html ?: null;
        $data['message'] = $text;
        $data['template'] = $data['template'] ?? 'classic';
        $data['button_text'] = ($data['button_text'] ?? '') ?: 'Shop now';
        $data['audience'] = $data['audience'] ?? 'all';
        $data['selected_users'] = $data['selected_users'] ?? [];
        if ($data['audience'] === 'selected' && ! $data['selected_users']) {
            throw ValidationException::withMessages(['selected_users' => 'Select at least one opted-in customer.']);
        }
        $images = array_values(array_filter($campaign->images ?? [], fn ($image, $index) => ! in_array($index, $data['remove_images'] ?? []), ARRAY_FILTER_USE_BOTH));
        if (count($images) + count($request->file('images', [])) > 4) {
            throw ValidationException::withMessages(['images' => 'Use up to four images per promotion.']);
        }
        foreach ($request->file('images', []) as $index => $file) {
            $path = $file->store('marketing', 'public');
            if (! $path) {
                throw ValidationException::withMessages(['images' => 'The picture could not be stored. Please try again.']);
            }
            $images[] = ['path' => $path, 'alt' => ($data['image_alt'][$index] ?? '') ?: 'AlbertinaNG promotion'];
        }
        $data['images'] = $images;
        unset($data['image_alt'],$data['remove_images']);
        // Store a brand snapshot with the draft so a later setting change cannot alter queued mail.
        $settings = [];
        try {
            $settings = Setting::pluck('value', 'key')->all();
        } catch (\Throwable $e) {
        }
        $logo = $settings['store_logo'] ?? null;
        $logoUrl = $logo ? (str_starts_with($logo, 'https://') ? $logo : Storage::disk('public')->url($logo)) : asset('image.png');
        if (! str_starts_with($logoUrl, 'https://')) {
            $logoUrl = null;
        }
        $data['brand'] = ['name' => 'AlbertinaNG', 'logo' => $logoUrl, 'address' => ($settings['store_address'] ?? '17-18 Zik\'s Avenue, Uwani, Enugu'), 'phone' => ($settings['store_phone'] ?? null), 'email' => 'promotions@albertinang.com'];
        $data['tested_at'] = null;

        return $data;
    }
}
