{{-- Shown in place of the review form when the visitor isn't a verified buyer
     (guest, or logged in but hasn't purchased this product). --}}
<div class="review-form-wrap" style="text-align:center;">
    <i class="fas fa-star" style="color:var(--g500);font-size:20px;"></i>
    <h3 style="margin:8px 0 4px;">{{ ($ratingCount ?? 0) === 0 ? 'Be the first to review' : 'Share your experience' }}</h3>
    <p style="color:#6b7280;font-size:13.5px;margin:0;">
        Only verified buyers can review this product — <strong>buy this product</strong> to leave your review.
    </p>
</div>
