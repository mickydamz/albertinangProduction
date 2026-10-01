@extends('layouts.app')

@section('content')


<div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">
                    <div class="container-fluid">
    <h2>User Transactions</h2>
    <canvas id="transactionChart"></canvas>

    <script>
            document.addEventListener('DOMContentLoaded', function () {
                fetch('/api/transactions')
                    .then(response => response.json())
                    .then(data => {
                        // Get today's date and yesterday's date
                        const today = new Date();
                        const yesterday = new Date(today);
                        yesterday.setDate(today.getDate() - 1);

                        // Aggregate total transaction amounts
                        const transactionsByDay = {
                            [yesterday.toLocaleDateString()]: 0, // Yesterday
                            [today.toLocaleDateString()]: 0, // Today
                        };

                        // Sum amounts by day
                        data.forEach(transaction => {
                            const transactionDate = new Date(transaction.created_at).toLocaleDateString();
                            if (transactionsByDay[transactionDate] !== undefined) {
                                transactionsByDay[transactionDate] += parseFloat(transaction.total_amount);
                            }
                        });

                        // Extract labels and amounts
                        const labels = Object.keys(transactionsByDay);
                        const amounts = Object.values(transactionsByDay);

                        // Set up the wave-like line chart
                        const ctx = document.getElementById('transactionChart').getContext('2d');
                        const transactionChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Transaction Amount',
                                    data: amounts,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    fill: false,
                                    lineTension: 0.8, // Increased for a wave effect
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                elements: {
                                    line: {
                                        tension: 0.8, // Further increase for more wave-like effect
                                    },
                                    point: {
                                        radius: 5 // Adjust point size
                                    }
                                },
                                plugins: {
                                    tooltip: {
                                        backgroundColor: 'rgba(0, 0, 0, 0.7)', // Tooltip background
                                        titleColor: '#fff',
                                        bodyColor: '#fff',
                                    },
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching transactions:', error);
                    });
            });
        </script>




                    <div class="container">
    <h1>Your Transactions</h1>

    @if($transactions->isEmpty())
        <p>You have no transactions.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                    <th>Products</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>${{ $transaction->total_amount }}</td>
                        <td>{{ $transaction->paymentMethod->name }}</td>
                        <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <ul>
                                @foreach($transaction->products as $product)
                                    <li>{{ $product->name }} (Quantity: {{ $product->pivot->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

</div>

</div>
@endsection
