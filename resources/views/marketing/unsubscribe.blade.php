<!doctype html><html lang="en"><head><meta charset="utf-8"><title>AlbertinaNG email preferences</title></head><body style="font-family:Arial;padding:40px;max-width:650px;margin:auto">
<h1>{{ !empty($done) ? 'You are unsubscribed' : 'Unsubscribe from promotions' }}</h1>
@if(!empty($done))<p>You will no longer receive promotional emails. Order and account emails will continue.</p>
@else<p>Stop receiving AlbertinaNG offers, sales and marketing emails.</p><form method="POST" action="{{ request()->fullUrl() }}">@csrf<button>Unsubscribe</button></form>@endif
</body></html>
