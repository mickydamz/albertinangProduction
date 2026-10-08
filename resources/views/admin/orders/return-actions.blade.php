<p>Return stage: <strong>{{ $return->stageLabel() }}</strong></p>
@if($return->refund_status)
<p>{{ \App\Support\RefundProgress::forStatus($return->refund_status)['label'] }}. <a href="{{ route('admin.refunds.index') }}">View refund progress</a></p>
@else
<div class="mb-2">
<label for="return-action-{{ $return->id }}" class="form-label">Next action</label>
<select id="return-action-{{ $return->id }}" name="action" class="form-select" required
        onchange="this.form.elements.return_instructions.required = this.value === 'approve'; this.form.elements.admin_notes.required = ['reject','inspect','refund_without_return'].includes(this.value);">
<option value="">Choose the next action</option>
@if($return->stage() === 'requested')<option value="approve">Approve return and send instructions</option>@endif
@if($return->stage() === 'approved')<option value="receive">Mark goods received</option>@endif
@if($return->stage() === 'received')<option value="inspect">Record successful inspection</option>@endif
@if($return->stage() === 'inspected')<option value="refund">Request full Paystack refund</option>@endif
@if(in_array($return->stage(), ['requested','approved','received']))<option value="reject">Reject return with a reason</option>@endif
@if(in_array($return->stage(), ['requested','approved','received','inspected']))<option value="refund_without_return">Exception: refund without return (reason required)</option>@endif
</select>
</div>
<div class="mb-2">
<label for="return-instructions-{{ $return->id }}" class="form-label">Return instructions (required for approval)</label>
<textarea id="return-instructions-{{ $return->id }}" name="return_instructions" class="form-control" rows="4" maxlength="2000" placeholder="Where to return the goods, what to include and how to arrange the return">{{ old('return_instructions', $return->return_instructions) }}</textarea>
<p class="small mt-1">These instructions are emailed to the customer and displayed in My Orders. Approval does not issue a refund.</p>
</div>
<div class="mb-2">
<label for="return-notes-{{ $return->id }}" class="form-label">Notes to customer (required for rejection, inspection or a refund exception)</label>
<textarea id="return-notes-{{ $return->id }}" name="admin_notes" class="form-control" rows="3" maxlength="1000" placeholder="For inspection, record what was checked and the outcome. These notes are sent to the customer.">{{ old('admin_notes', $return->admin_notes) }}</textarea>
@if($return->stage() === 'received')
<p class="small mt-1">Enter the inspection outcome above, then save the decision. After a successful inspection, review the return again to request the refund.</p>
@endif
</div>
@endif
