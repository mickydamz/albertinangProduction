<!doctype html><html lang="en"><head><meta charset="utf-8"><title>{{ $campaign->subject }}</title></head>
<body style="font-family:Arial,sans-serif;background:#f4f5f7;padding:24px;color:#222"><main style="max-width:600px;margin:auto;background:white;padding:32px;border-radius:12px">
<p style="color:#177855;font-weight:bold">AlbertinaNG · Offers &amp; promotions</p><h1>{{ $campaign->subject }}</h1>
<div style="white-space:pre-line;line-height:1.7">{{ $campaign->message }}</div>
@if($campaign->offer_url)<p><a href="{{ $campaign->offer_url }}" style="color:#177855">View offer</a></p>@endif
<hr><p style="font-size:12px">You opted in to AlbertinaNG promotional emails. <a href="{{ $unsubscribeUrl }}">Unsubscribe</a></p>
</main></body></html>
