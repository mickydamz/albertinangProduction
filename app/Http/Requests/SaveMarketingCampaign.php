<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveMarketingCampaign extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'subject' => ['required', 'string', 'max:150', 'not_regex:/[\r\n]/'], 'message' => 'nullable|string|max:20000', 'body_html' => 'nullable|string|max:60000',
            'preview_text' => 'nullable|string|max:200', 'button_text' => 'nullable|string|max:60', 'offer_url' => 'nullable|url|max:2048|regex:~^https://~i',
            'button_alignment' => ['nullable', Rule::in(['left', 'center', 'right'])],
            'template' => ['nullable', Rule::in(['classic', 'sale', 'new-arrivals'])], 'audience' => ['nullable', Rule::in(['all', 'recent', 'selected'])],
            'selected_users' => 'nullable|array|max:1000', 'selected_users.*' => 'integer|distinct|exists:users,id',
            'images' => 'nullable|array|max:4', 'images.*' => 'required|image|mimes:jpg,jpeg,png|max:3072|dimensions:max_width=4000,max_height=4000',
            'image_alt' => 'nullable|array|max:4', 'image_alt.*' => 'nullable|string|max:200', 'remove_images' => 'nullable|array|max:4', 'remove_images.*' => 'integer|min:0|max:3',
        ];
    }
}
