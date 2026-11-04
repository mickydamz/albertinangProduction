@php
 $colourChoices=\App\Support\ProductColours::options($product);
 $selectedColour=collect($colourChoices)->firstWhere('id',(int)request('colour'));
 if(!$selectedColour && count($colourChoices)===1)$selectedColour=$colourChoices[0];
@endphp
@if(count($colourChoices))
<fieldset id="productColourChoices" style="border:0;padding:0;margin:16px 0">
<legend style="font-size:13px;font-weight:700;margin-bottom:9px">Colour: <span id="chosenColourName">{{ $selectedColour['name'] ?? 'Choose a colour' }}</span></legend>
<div style="display:flex;gap:9px;flex-wrap:wrap">
@foreach($colourChoices as $colour)<label style="cursor:pointer;display:flex;align-items:center;gap:6px;padding:7px 10px;border:1px solid #b7c1b0;border-radius:20px;font-size:12px">
<input type="radio" name="product_colour" value="{{ $colour['id'] }}" data-colour-name="{{ $colour['name'] }}" @checked(($selectedColour['id']??null)===$colour['id'])>
<span aria-hidden="true" style="width:18px;height:18px;border:1px solid #9ca3af;border-radius:50%;background:{{ $colour['swatch'] }}"></span>{{ $colour['name'] }}</label>@endforeach
</div><small>Same price for every colour.</small><p id="colourChoiceError" role="alert" style="color:#b91c1c;font-size:12px"></p>
</fieldset>
@endif
