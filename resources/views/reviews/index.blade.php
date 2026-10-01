{{-- resources/views/reviews/create.blade.php --}}
<form action="{{ route('reviews.store', $product->id) }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="content">Review</label>
        <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
    </div>

    <div class="form-group">
        <label for="rating">Rating</label>
        <select name="rating" id="rating" class="form-control" required>
            <option value="">Select a rating</option>
            <option value="1">1 Star</option>
            <option value="2">2 Stars</option>
            <option value="3">3 Stars</option>
            <option value="4">4 Stars</option>
            <option value="5">5 Stars</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Submit Review</button>
</form>
