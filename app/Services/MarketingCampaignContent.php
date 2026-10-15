<?php

namespace App\Services;

use App\Http\Requests\SaveMarketingCampaign;
use App\Models\MarketingCampaign;
use Illuminate\Validation\ValidationException;

class MarketingCampaignContent
{
    public static function data(SaveMarketingCampaign $request, ?MarketingCampaign $campaign = null): array
    {
        $data = $request->validated();
        $html = MarketingHtml::clean($data['body_html'] ?? null);
        $text = $html ? MarketingHtml::text($html) : trim($data['message'] ?? '');
        $data['body_html'] = $text !== '' ? ($html ?: null) : null;
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
        // Retain draft branding metadata; email contact details resolve from Settings when rendered.
        $data['brand'] = MarketingBrand::current();
        $data['offer_url'] = ($data['offer_url'] ?? '') ?: url('/shop');
        $data['tested_at'] = null;

        return $data;
    }
}
