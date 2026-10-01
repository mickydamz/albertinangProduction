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
                                <!-- Check if there are transactions -->
                                @if($transactions->isEmpty())
                                    <div class="alert alert-secondary" role="alert">
                                        You have no transactions yet.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-styled">
                                            <thead>
                                                <tr>
                                                    <th>Total Amount</th>
                                                    <th>Buyer</th>
                                                    <th>Date</th>
                                                    <th>Products</th>
                                                    <th>Quantity</th>
                                                    <th>Payment Method</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td data-label="Total Amount">
                                                            <span class="amount-label">${{ number_format($transaction->total_amount, 2) }}</span>
                                                        </td>
                                                        <td data-label="Buyer">
                                                            <span class="amount-label">{{ $transaction->user->name }}</span>
                                                        </td>
                                                        <td data-label="Date" class="text-muted transaction-date">
                                                            <i class="fas fa-calendar-alt me-1"></i>{{ $transaction->created_at->format('Y-m-d H:i') }}
                                                        </td>
                                                        <td data-label="Products">
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach($transaction->products as $product)
                                                                    <li>
                                                                        <i class="fas fa-shopping-cart me-1 text-info"></i>{{ $product->name }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </td>
                                                        <td data-label="Quantity">
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach($transaction->products as $product)
                                                                    <li>
                                                                        <span class="badge rounded-pill bg-primary ms-2">{{ $product->pivot->quantity }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
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
                                                        <td data-label="Status" class="text-muted">
                                                            @php
                                                                // Define status labels and corresponding classes
                                                                $statusLabels = [
                                                                    '1' => ['label' => 'Completed', 'class' => 'bg-success'],
                                                                    '0' => ['label' => 'Pending', 'class' => 'bg-warning'],
                                                                    '2' => ['label' => 'Failed', 'class' => 'bg-danger'],
                                                                ];

                                                                // Get the status info based on the transaction status
                                                                $statusInfo = $statusLabels[$transaction->status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                                            @endphp

                                                            <span class="badge {{ $statusInfo['class'] }}">
                                                                {{ $statusInfo['label'] }}
                                                            </span>
                                                        </td>
                                                        <td data-label="Actions">
                                                            <a href="{{ route('chat.show', $transaction->user->id) }}" class="btn btn-sm btn-primary ms-2" title="Contact buyer and provide shipping details">
                                                                <i class="fas fa-comment-dots me-1"></i> Contact buyer
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <tr class="transaction-details" id="details-{{ $transaction->id }}" style="display: none;">
                                                        <td colspan="8">
                                                            <strong>Transaction Details:</strong>
                                                            <p>Additional information about transaction ID {{ $transaction->id }}.</p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
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
    .amount-label {
        font-weight: 500;
        font-size: 0.9rem;
        color: #007bff;
    }

    .badge {
        font-size: 0.75rem;
        font-weight: 600;
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
        const toggleButtons = document.querySelectorAll('.toggle-details');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const transactionId = this.getAttribute('data-id');
                const detailsRow = document.getElementById(`details-${transactionId}`);
                if (detailsRow.style.display === 'none') {
                    detailsRow.style.display = 'table-row';
                    this.querySelector('i').classList.remove('fa-plus');
                    this.querySelector('i').classList.add('fa-minus');
                } else {
                    detailsRow.style.display = 'none';
                    this.querySelector('i').classList.remove('fa-minus');
                    this.querySelector('i').classList.add('fa-plus');
                }
            });
        });

        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

@endsection