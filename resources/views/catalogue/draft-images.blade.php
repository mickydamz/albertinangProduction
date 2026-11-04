@if($revision->exists)
<details class="mb-2"><summary>Existing and saved images</summary><p>Tick an image to remove it when this submission is approved.</p><div class="d-flex flex-wrap gap-2">
@foreach($revision->base_snapshot['images'] ?? [] as $image)<label><img class="ce-photo" alt="Existing product image" src="{{ asset('storage/'.$image['image_url']) }}"><input type="checkbox" name="remove_images[]" value="{{ $image['id'] }}" @checked(in_array($image['id'],old('remove_images',$revision->payload['remove_images'] ?? [])))> Remove existing image</label>@endforeach
@foreach($revision->uploads()->where('kind','gallery')->get() as $upload)<label><img class="ce-photo" alt="Saved draft image" src="{{ asset('storage/'.$upload->path) }}"><input type="checkbox" name="remove_uploads[]" value="{{ $upload->id }}" @checked(in_array($upload->id,old('remove_uploads',$revision->payload['remove_uploads'] ?? [])))> Remove draft image</label>@endforeach</div></details>
@endif
