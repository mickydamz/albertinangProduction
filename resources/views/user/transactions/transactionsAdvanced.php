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
        <div class="content-header row">
        </div>
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
                            <table class="table table-bordered table-hover table-styled">
                                <thead>
                                    <tr>
                                        <th>Total Amount</th>
                                        <th>Escrow Amount</th>
                                        <th>Date</th>
                                        <th>Products</th>
                                        <th>Status</th> <!-- New column for status -->
                                        <th>Actions</th> <!-- Add an action column for toggling details -->
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
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($transaction->products as $product)
                                                        <li>
                                                            <i class="fas fa-shopping-cart me-1 text-info"></i>{{ $product->name }}
                                                            <span class="badge rounded-pill bg-primary ms-2">{{ $product->pivot->quantity }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
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
                                                <button class="btn btn-sm btn-info toggle-details" data-id="{{ $transaction->id }}">
                                                    <i class="fas fa-plus"></i> View Details
                                                </button>
                                            </td>
                                        </tr>

                                        <tr class="transaction-details" id="details-{{ $transaction->id }}" style="display: none;">
                                            <td colspan="6">
                                                <strong>Transaction Details:</strong>
                                                <p>Additional information about transaction ID {{ $transaction->id }}.</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<style>
    .amount-label {
        font-weight: 500;
        font-size: 0.9rem;
        color: #007bff;
    }

    /* Styling for transaction actions */
    .toggle-details {
        cursor: pointer;
    }

    .transaction-details td {
        background-color: #f9f9f9;
        font-style: italic;
    }

    /* Additional styling */
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f5f5f5;
    }

    .card {
        border: none;
    }

    .table-styled {
        background-color: #fff;
        color: #343a40;
    }

    .table-styled thead {
        color: grey;
        font-weight: 700;
    }

    .table-styled tbody tr {
        transition: background-color 0.3s;
    }

    .table-styled tbody tr:hover {
        background-color: #e2f0ff;
    }

    .transaction-date {
        color: #495057;
    }

    .badge {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .table-styled td {
        padding: 1rem;
        font-size: 0.9rem;
    }

    /* Responsive adjustments */
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
                const icon = this.querySelector('i');
                
                if (detailsRow.style.display === 'none') {
                    detailsRow.style.display = 'table-row';
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                } else {
                    detailsRow.style.display = 'none';
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            });
        });
    });
</script>

@endsection
