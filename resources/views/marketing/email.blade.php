@php($accent = $campaign->template === 'sale' ? '#a44620' : ($campaign->template === 'new-arrivals' ? '#234d73' : '#177855'))
@php($kicker = $campaign->template === 'sale' ? 'SPECIAL OFFERS' : ($campaign->template === 'new-arrivals' ? 'NEW & NOTEWORTHY' : 'OFFERS & UPDATES'))
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>{{ $campaign->subject }}</title>
<style>@media only screen and (max-width:600px){.email-pad{padding:24px!important}.email-shell{width:100%!important}.email-title{font-size:28px!important}.email-picture{width:100%!important;display:block!important}}</style></head>
<body style="margin:0;padding:0;background:#f3f5f2;font-family:Arial,Helvetica,sans-serif;color:#25312a">
<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden">{{ $campaign->preview_text }}</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f5f2"><tr><td align="center" style="padding:24px 10px">
<table class="email-shell" role="presentation" width="640" cellspacing="0" cellpadding="0" style="width:640px;max-width:100%;background:white;border-radius:16px;overflow:hidden">
<tr><td align="center" style="padding:28px 24px;border-top:6px solid {{ $accent }}">
@if(!empty($brand['logo']))<img src="{{ $brand['logo'] }}" alt="AlbertinaNG" width="190" style="display:block;max-width:190px;height:auto;border:0;margin:0 auto">@else<strong style="font-size:26px;color:#177855">AlbertinaNG</strong>@endif
<p style="margin:12px 0 0;font-size:11px;letter-spacing:3px;color:{{ $accent }}">{{ $kicker }}</p></td></tr>
@if($isTest)<tr><td style="padding:12px 28px;background:#fff5d7;color:#654b11">TEST EMAIL — for internal review</td></tr>@endif
@if(!empty($campaign->images[0]))<tr><td><img src="{{ \Storage::disk('public')->url($campaign->images[0]['path']) }}" alt="{{ $campaign->images[0]['alt'] }}" width="640" style="display:block;width:100%;height:auto;border:0"></td></tr>@endif
<tr><td class="email-pad" style="padding:36px 40px"><h1 class="email-title" style="font-size:34px;line-height:1.2;margin:0 0 24px;color:#213a28">{{ $campaign->subject }}</h1>
<div style="font-size:16px;line-height:1.75;color:#37453c">@if($safeHtml){!! $safeHtml !!}@else<div style="white-space:pre-line">{{ $campaign->message }}</div>@endif</div>
@if($offerUrl)<table role="presentation" cellspacing="0" cellpadding="0" style="margin:28px 0 4px"><tr><td style="border-radius:7px;background:{{ $accent }}"><a href="{{ $offerUrl }}" style="display:inline-block;padding:16px 28px;font-size:16px;font-weight:bold;color:#fff;text-decoration:none">{{ $campaign->button_text ?: 'Shop now' }} →</a></td></tr></table>@endif
</td></tr>
@foreach(array_slice($campaign->images ?? [],1) as $image)<tr><td style="padding:0 32px 24px"><img src="{{ \Storage::disk('public')->url($image['path']) }}" alt="{{ $image['alt'] }}" width="576" style="display:block;width:100%;height:auto;border-radius:10px;border:0"></td></tr>@endforeach
<tr><td align="center" style="padding:28px 32px;background:#edf3e9;border-top:1px solid #dae4d5;font-size:12px;line-height:1.8;color:#586453"><strong style="color:#285831;font-size:16px">AlbertinaNG</strong><br>{{ $brand['address'] ?? '' }}<br>@if(!empty($brand['phone'])){{ $brand['phone'] }} · @endif<a href="mailto:promotions@albertinang.com" style="color:#285831">promotions@albertinang.com</a>
<p style="margin:16px 0 0">You opted in to AlbertinaNG promotional emails.<br><a href="{{ $unsubscribeUrl }}" style="color:#285831">Unsubscribe</a> · <a href="{{ route('marketing.preferences') }}" style="color:#285831">Email preferences</a></p>
<p style="margin:12px 0 0">© {{ date('Y') }} AlbertinaNG</p></td></tr>
</table></td></tr></table></body></html>
