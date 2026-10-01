<div>
    <h3>Upload Transaction Receipt</h3>

    <!-- Form to submit the deposit -->
    <form wire:submit.prevent="submit">
        <!-- Transaction Receipt Upload Form -->
        <div class="form-group mt-3">
            <label for="transaction_picture" class="form-label">Upload Transaction Receipt</label>
            <input type="file" wire:model="transactionPicture" id="transaction_picture" class="form-control" accept="image/*" required>
            <small class="form-text text-muted">Please upload the receipt to confirm your payment. Allow up to 24 hours for the payment to reflect in your account after submission.</small>
        </div>

        <!-- Display validation errors -->
        @error('transactionPicture')
            <div class="alert alert-danger mt-2">{{ $message }}</div>
        @enderror

        <!-- Submit Button -->
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <!-- Display Success Message -->
    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
</div>
