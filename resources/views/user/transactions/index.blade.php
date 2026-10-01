@extends('layouts.app')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row"></div>
        <div class="content-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title"><i class="fas fa-exchange-alt me-2 text-info"></i>Transaction Management</h4>
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <li><a data-action="collapse"><i data-feather="chevron-down"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content collapse show">
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if($transactions->isEmpty())
                                        <div class="alert alert-secondary text-center">
                                            <i class="fas fa-info-circle me-2"></i> You have no transactions yet.
                                        </div>
                                    @else
                                        <table class="table table-bordered table-hover table-styled">
                                            <thead>
                                                <tr>
                                                    <th>Total Amount</th>
                                                    <th>Escrow Amount</th>
                                                    <th>Payment Method</th>
                                                    <th>Date</th>
                                                    <th>Products</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td data-label="Total Amount">
                                                            <span class="amount-label">${{ number_format($transaction->total_amount, 2) }}</span>
                                                        </td>
                                                        <td data-label="Escrow Amount">
                                                            <span class="amount-label">${{ number_format($transaction->escrow_amount, 2) }}</span>
                                                        </td>
                                                        <td data-label="Payment Method">
                                                            @if($transaction->payment_method_id == 1)
                                                                <span class="badge bg-primary">Full Payment</span>
                                                            @elseif($transaction->payment_method_id == 2)
                                                                <span class="badge bg-info">Half Payment</span>
                                                            @elseif($transaction->payment_method_id == 3)
                                                                <span class="badge bg-warning">Escrow</span>
                                                            @else
                                                                <span class="badge bg-secondary">Unknown</span>
                                                            @endif
                                                        </td>
                                                        <td data-label="Date" class="text-muted transaction-date">
                                                            <i class="fas fa-calendar-alt me-1"></i>{{ $transaction->created_at->format('Y-m-d H:i') }}
                                                        </td>
                                                        <td data-label="Products">
    @php
        $groupedProducts = $transaction->products->groupBy('supplier_id');
    @endphp

    <ul class="list-unstyled mb-0">
        @foreach($groupedProducts as $supplierId => $products)
            @php
                $supplier = $products->first()->supplier ?? null;
            @endphp
            <li class="mb-2">
                <i class="fas fa-user-tie me-1 text-success"></i>
                <strong>{{ $supplier ? $supplier->name : 'Unknown Supplier' }}</strong>

                <ul class="list-unstyled ms-3">
                    @foreach($products as $product)
                        <li>
                            <i class="fas fa-shopping-cart me-1 text-info"></i>
                            <span 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="{{ $product->name }}">
                                {{ \Illuminate\Support\Str::limit($product->name, 25) }}
                            </span>
                            <span class="badge rounded-pill bg-primary ms-2">{{ $product->pivot->quantity }}</span>
                        </li>
                    @endforeach
                </ul>

                @if($supplier)
                <a href="{{ route('chat.show', $supplier->id) }}" class="btn btn-sm btn-primary mt-2"
   data-bs-toggle="tooltip" 
   data-bs-placement="top" 
   title="Contact supplier for shipping details">
   <i class="fas fa-comment-dots me-1"></i> Chat
</a>

                @endif
            </li>
            <hr>
        @endforeach
    </ul>
</td>


                                                        <td data-label="Status" class="text-muted">
                                                            @php
                                                                $statusLabels = [
                                                                    '1' => ['label' => 'Completed', 'class' => 'bg-success'],
                                                                    '0' => ['label' => 'Pending', 'class' => 'bg-warning'],
                                                                    '2' => ['label' => 'Failed', 'class' => 'bg-danger'],
                                                                ];
                                                                $statusInfo = $statusLabels[$transaction->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                                            @endphp
                                                            <span class="badge {{ $statusInfo['class'] }}">
                                                                {{ $statusInfo['label'] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END: Content-->

<style>
    .supplier-details {
        margin-top: 5px;
        padding: 5px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    .toggle-details {
        color: #007bff;
        text-decoration: none;
    }
    .toggle-details:hover {
        text-decoration: underline;
    }
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }
        .table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
        }
        .table td {
            display: block;
            text-align: right;
            position: relative;
            padding: 0.5rem;
            border: none;
        }
        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            text-align: left;
            font-weight: bold;
            color: #6c757d;
            padding: 0.5rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-details').forEach(button => {
            button.addEventListener('click', function() {
                const details = document.getElementById(`details-${this.dataset.id}`);
                details.style.display = details.style.display === 'none' ? 'block' : 'none';
                this.querySelector('i').classList.toggle('fa-chevron-down');
                this.querySelector('i').classList.toggle('fa-chevron-up');
            });
        });

        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>


<style>
    @media (max-width: 768px) {
    .table td {
        display: block;
        text-align: left;
        padding-left: 40%;
        position: relative;
        white-space: normal;
        word-wrap: break-word;
    }
    
    .table td::before {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: bold;
        color: #6c757d;
        width: 35%;
        white-space: normal;
        word-wrap: break-word;
    }

    /* Ensure supplier and product names appear on separate lines */
    .supplier-details, .list-unstyled li {
        display: block;
        width: 100%;
        white-space: normal;
        word-wrap: break-word;
    }
}

</style>

@endsection