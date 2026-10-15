@php($editing = isset($campaign))
@php($draft = $campaign ?? new \App\Models\MarketingCampaign)
<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.marketing.update', $draft) : route('admin.marketing.store') }}" data-promotion-editor>
@csrf @if($editing) @method('PUT') @endif
<div class="row"><div class="col-lg-8">
<label for="subject">Email subject</label><input class="form-control mb-2" id="subject" name="subject" maxlength="150" value="{{ old('subject', $draft->subject) }}" required>
<label for="preview_text">Inbox preview text</label><input class="form-control mb-2" id="preview_text" name="preview_text" maxlength="200" value="{{ old('preview_text', $draft->preview_text) }}" placeholder="A short introduction shown beside the subject">
<label id="message-label">Promotion message (optional)</label>
<div class="promo-tools" role="toolbar" aria-label="Message formatting">
<button type="button" data-command="bold" aria-label="Bold"><b>B</b></button><button type="button" data-command="italic" aria-label="Italic"><i>I</i></button><button type="button" data-command="underline" aria-label="Underline"><u>U</u></button>
<button type="button" data-command="formatBlock" data-value="h2">Heading</button><button type="button" data-command="formatBlock" data-value="p">Paragraph</button>
<button type="button" data-command="insertUnorderedList">Bullets</button><button type="button" data-command="insertOrderedList">Numbers</button>
<button type="button" data-command="justifyLeft" aria-label="Align left">Left</button><button type="button" data-command="justifyCenter" aria-label="Align centre">Centre</button>
<button type="button" data-link>Add link</button><button type="button" data-command="unlink">Remove link</button><button type="button" data-command="removeFormat">Clear format</button>
<label class="mb-0">Text colour <input type="color" value="#177855" data-text-color aria-label="Text colour"></label>
</div>
<div class="promo-editor" contenteditable="true" role="textbox" aria-labelledby="message-label" aria-multiline="true" data-rich-message>{!! \App\Services\MarketingHtml::clean(old('body_html', $draft->body_html)) ?: nl2br(e(old('message', $draft->message))) !!}</div>
<textarea name="body_html" data-html-output hidden></textarea>
<noscript><label for="plain-message">Plain text message</label><textarea class="form-control" id="plain-message" name="message" rows="8">{{ old('message', $draft->message) }}</textarea></noscript>
<p class="text-muted small mt-1">Leave blank for a picture-only promotion. Select text to format it; pasted text is cleaned before saving.</p>
</div><div class="col-lg-4">
<label for="template">Email layout</label><select class="form-control mb-2" id="template" name="template">
@foreach(['classic'=>'Classic company update','sale'=>'Sale & special offer','new-arrivals'=>'New arrivals'] as $value=>$label)<option value="{{ $value }}" @selected(old('template',$draft->template ?? 'classic')===$value)>{{ $label }}</option>@endforeach
</select>
<div class="promo-brand mb-2"><strong>AlbertinaNG</strong><br><span>From: promotions@albertinang.com</span><br><span>Company logo and contact footer included</span></div>
<label for="button_text">Offer button text</label><input class="form-control mb-2" id="button_text" name="button_text" maxlength="60" value="{{ old('button_text',$draft->button_text ?? 'Shop now') }}">
<label for="offer_url">Offer destination (HTTPS)</label><input class="form-control mb-2" type="url" id="offer_url" name="offer_url" value="{{ old('offer_url',$draft->offer_url ?: url('/shop')) }}" placeholder="https://test.albertinang.com/shop">
<label for="audience">Recipients</label><select class="form-control mb-2" id="audience" name="audience">
@foreach(['all'=>'All opted-in customers','recent'=>'Customers who opted in during the last 30 days','selected'=>'Selected opted-in customers'] as $value=>$label)<option value="{{ $value }}" @selected(old('audience',$draft->audience ?? 'all')===$value)>{{ $label }}</option>@endforeach
</select>
@if(isset($customers))
<label for="selected_users">Choose customers (for selected audience)</label><select class="form-control mb-2" id="selected_users" name="selected_users[]" multiple size="5">
@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(in_array($customer->id,old('selected_users',$draft->selected_users ?? [])))>{{ $customer->name }} — {{ $customer->email }}</option>@endforeach
</select><p class="small text-muted">Only opted-in customer accounts are shown, up to 1,000. Hold Ctrl or Command to select several.</p>
@else<p class="small text-muted">Save your draft to choose individual customers.</p>@endif
</div></div>
<hr><h3>Promotion pictures</h3><p class="text-muted">The first picture is your main banner. Use up to four JPG or PNG pictures, up to 3 MB each. Images are public promotional assets.</p>
<div class="row">
@foreach($draft->images ?? [] as $index=>$image)<div class="col-sm-3 mb-2"><img src="{{ \Storage::disk('public')->url($image['path']) }}" alt="{{ $image['alt'] }}" style="max-width:100%;height:120px;object-fit:contain"><p class="small">{{ $image['alt'] }}</p><label><input type="checkbox" name="remove_images[]" value="{{ $index }}"> Remove picture</label></div>@endforeach
</div>
<label for="promo-images">Upload pictures</label><input class="form-control mb-2" id="promo-images" type="file" name="images[]" accept="image/jpeg,image/png" multiple data-image-input>
<div class="row" data-image-previews></div>
<details class="mb-3"><summary>Describe new pictures for readers who cannot see them</summary><div class="row mt-2">@for($i=0;$i<4;$i++)<div class="col-sm-6"><label for="image-alt-{{ $i }}">New picture {{ $i+1 }} description</label><input class="form-control mb-2" id="image-alt-{{ $i }}" name="image_alt[]" maxlength="200" value="{{ old('image_alt.'.$i) }}"></div>@endfor</div></details>
<button class="btn btn-primary">{{ $editing ? 'Save changes' : 'Save draft & preview' }}</button>
</form>
