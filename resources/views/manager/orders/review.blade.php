@php
 $stage=$reviewType==='return'?$reviewRecord->stage():$reviewRecord->status;
 $available=$reviewType==='return' ? ['requested'=>['approve','reject'],'approved'=>['receive','reject'],'received'=>['inspect','reject'],'inspected'=>['refund']] : ['pending'=>['approve','reject'],'approved'=>['refund']];
 $actions=$reviewRecord->refund_status ? [] : ($available[$stage]??[]);
@endphp
<div class="card"><div class="card-body">
 <h4>{{ ucfirst($reviewType) }} review</h4>
 <p><strong>{{ ucwords(str_replace('_',' ',$stage)) }}</strong><br>{{ $reviewRecord->reason }}</p>
 @if($reviewRecord->return_instructions)<p>Return instructions: {{ $reviewRecord->return_instructions }}</p>@endif
 @if($reviewRecord->refund_status)<p>Refund: {{ $reviewRecord->refund_status }}@if($reviewRecord->refund_id) · {{ $reviewRecord->refund_id }}@endif</p><p>Pending or uncertain refunds require reconciliation before another refund can be requested.</p>@endif
 @if($actions)
 <form method="post" action="{{ route('manager.orders.review',[$order,$reviewType]) }}">@csrf
  <label class="form-label" for="{{ $reviewType }}-action">Action</label><select class="form-select mb-1" id="{{ $reviewType }}-action" name="action">@foreach($actions as $action)<option value="{{ $action }}">{{ ['approve'=>'Approve request','reject'=>'Reject request','receive'=>'Confirm goods received','inspect'=>'Confirm inspection completed','refund'=>'Issue / record full refund'][$action] }}</option>@endforeach</select>
  <label class="form-label" for="{{ $reviewType }}-reason">Review notes / inspection findings</label><textarea id="{{ $reviewType }}-reason" name="reason" class="form-control mb-1" required maxlength="1000"></textarea>
  @if($reviewType==='return' && in_array('approve',$actions))<label class="form-label" for="return-instructions">Return instructions (required when approving)</label><textarea id="return-instructions" name="return_instructions" class="form-control mb-1" maxlength="2000"></textarea>@endif
  @if(in_array('refund',$actions))
   @if(!in_array($order->payment_method,['paystack','stripe']))<p>Pay the customer through the original offline method first, then record the reference here.</p><label for="{{ $reviewType }}-refund-ref" class="form-label">Offline refund reference</label><input id="{{ $reviewType }}-refund-ref" name="refund_reference" class="form-control mb-1" maxlength="255" required>@else<p>This requests a full refund through {{ ucfirst($order->payment_method) }}. The order is marked refunded only after confirmation.</p>@endif
   <label class="d-block mb-1"><input type="checkbox" name="refund_confirmed" value="1" required> I confirm this full refund is authorised{{ !in_array($order->payment_method,['paystack','stripe'])?' and the customer has received it':'' }}.</label>
  @endif
  <button class="btn btn-outline-primary">Save {{ $reviewType }} review</button>
 </form>
 @endif
</div></div>
