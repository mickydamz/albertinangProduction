<form wire:submit.prevent="submit" enctype="multipart/form-data">
    <div>
        @if(session()->has('successmessage'))
            <div class="alert alert-success">
                {{ session('successmessage') }}
            </div>
        @endif
    </div>

    <div class="form-group">
        <label for="fileName">Profile Picture</label>
        <input type="file" class="form-control" wire:model="fileName">
        @error('fileName') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="btn btn-primary mt-3">Upload</button>

    <div wire:loading>
        Processing...
    </div>

    <hr>
    
  

</form>
