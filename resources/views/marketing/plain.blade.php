AlbertinaNG — {{ $campaign->subject }}
@if($isTest)TEST EMAIL — for internal review
@endif
{{ $campaign->message }}
@if($offerUrl)
{{ $campaign->button_text ?: 'Shop now' }}: {{ $offerUrl }}
@endif
{{ $brand['address'] ?? '' }}
promotions@albertinang.com

You opted in to AlbertinaNG promotional emails.
Unsubscribe: {{ $unsubscribeUrl }}
Email preferences: {{ route('marketing.preferences') }}
