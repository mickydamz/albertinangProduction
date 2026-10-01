@extends('layouts.app')

@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
            </div>
            <div class="content-body">


                <!-- Dashboard Ecommerce Starts -->
                <section id="dashboard-ecommerce">

                <div class="row match-height">
                        <!-- Medal Card -->
                        <div class="col-xl-4 col-md-6 col-12">
    <div class="card card-congratulation-medal">
        <div class="card-body">
            <h5>Welcome {{ auth()->user()->name }},</h5>
            <p class="card-text font-small-3">Albertina</p>
            <!-- <h3 class="mb-75 mt-2 pt-50">
                <a href="/page-account-settings-account" class="btn btn-primary">View Profile</a>
            </h3> -->
            <img src="{{ asset('app-assets/images/illustration/badge.svg') }}" class="congratulation-medal" alt="Medal Pic" />
        </div>
    </div>
</div>


                        <div class="col-xl-4 col-md-6 col-12">
                                    <div class="card earnings-card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h4 class="card-title mb-1">Earnings</h4>
                                                    <div class="font-small-2">Total</div>
                                                    <h5 class="mb-1">${{ number_format(10,2) }}</h5>

                                                   
                                                    <p class="card-text text-muted font-small-2">
                                                        <span class="fw-bolder">    </span><span>account balance in usd</span>
                                                    </p>
                                                </div>
                                                <!-- <div class="col-6">
                                                    <div id="earnings-chart"></div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-md-6 col-12">
    <div class="card earnings-card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <h4 class="card-title mb-1">Referral Link</h4>
                    <div class="font-small-2">Total</div>
                    <div class="input-group mb-2" style="width: 100%;">
                        <input 
                            type="text" 
                            class="form-control form-control-sm" 
                            id="referralLink" 
                            value="{{ $referralLink }}" 
                            readonly 
                            style="font-size: 0.875rem; padding: 0.5rem;"/>
                        <button 
                            class="btn btn-outline-secondary btn-sm" 
                            onclick="copyReferralLink()" 
                            style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                            Copy
                        </button>
                    </div>
                    <p class="card-text text-muted font-small-2">
                        <span class="fw-bolder"></span><span>number of people referred.</span>
                    </p>
                </div>
                <!-- <div class="col-6">
                    <div id="earnings-chart"></div>
                </div> -->
            </div>
        </div>
    </div>
</div>

<script>
function copyReferralLink() {
    var referralLink = document.getElementById("referralLink");
    referralLink.select();
    referralLink.setSelectionRange(0, 99999); // For mobile devices
    navigator.clipboard.writeText(referralLink.value).then(() => {
        alert("Referral link copied to clipboard!");
    });
}
</script>

                    <div class="row match-height">
                        <div class="col-lg-12 col-12">
                            <div class="row match-height">

                                <!-- Bar Chart - Orders -->
                                <div class="col-lg-6 col-md-3 col-6">
                                    <div class="card">
                                        <div class="card-body pb-50">
                                            <h6>Commissions</h6>
                                            <h2 class="fw-bolder mb-1">0.0</h2>
                                            <div id="statistics-order-chart"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--/ Bar Chart - Orders -->

                                <!-- Line Chart - Profit -->
                                <div class="col-lg-6 col-md-3 col-6">
                                    <div class="card card-tiny-line-stats">
                                        <div class="card-body pb-50">
                                            <h6>All time commissions</h6>
                                            <h2 class="fw-bolder mb-1">0.0</h2>
                                            <div id="statistics-profit-chart"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--/ Line Chart - Profit -->
                            </div>
                        </div>
                    </div>


                    <!-- New Table with Commissions, Clicks, Items, Earnings -->
<div class="row match-height">
    <div class="col-12">
        <h2 class="mb-3">Affiliate Metrics</h2>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover" id="affiliate-metrics-table">
                    <thead>
                        <tr>
                            <!-- <th>Name</th> -->
                            <th>Commissions</th>
                            <th>Total Clicks</th>
                            <th>Total Items Shipped</th>
                            <th>Total Earnings</th>
                            <th>Total Ordered Items</th>
                            <th>Clicks</th>
                            <th>Conversions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <!-- <td data-label="Name">{{ auth()->user()->name }}</td> -->
                            <td data-label="Commissions">${{ number_format(auth()->user()->commissions, 2) }}</td>
                            <td data-label="Total Clicks">{{ auth()->user()->total_clicks }}</td>
                            <td data-label="Total Items Shipped">{{ auth()->user()->total_items_shipped }}</td>
                            <td data-label="Total Earnings">${{ number_format(auth()->user()->total_earnings, 2) }}</td>
                            <td data-label="Total Ordered Items">{{ auth()->user()->total_orders }}</td>
                            <td data-label="Clicks">{{ auth()->user()->clicks }}</td>
                            <td data-label="Conversions">{{ auth()->user()->conversions }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Basic table styling */
    #affiliate-metrics-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    #affiliate-metrics-table th, 
    #affiliate-metrics-table td {
        padding: 10px;
        text-align: left;
    }

    #affiliate-metrics-table th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: bold;
        border-bottom: 2px solid #ddd;
    }

    #affiliate-metrics-table tbody tr {
        background-color: #ffffff;
        transition: background-color 0.3s ease;
    }

    #affiliate-metrics-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        /* Make each table row a block on small screens */
        #affiliate-metrics-table thead {
            display: none; /* Hide the header */
        }

        #affiliate-metrics-table tr {
            display: block; /* Make each row a block */
            margin-bottom: 1rem;
            border: 1px solid #ddd; /* Add a border for better separation */
        }

        #affiliate-metrics-table td {
            display: block; /* Make each cell a block */
            text-align: right; /* Align text to the right */
            position: relative;
            padding: 0.75rem;
            border: none;
        }

        #affiliate-metrics-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            font-weight: bold;
            text-align: left;
            color: #6c757d;
            padding: 0.75rem;
        }

        #affiliate-metrics-table td:first-child {
            padding-top: 1.25rem;
        }
    }

    /* Styling for the table rows */
    #affiliate-metrics-table tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }

    #affiliate-metrics-table tbody tr:nth-child(even) {
        background-color: #f1f1f1;
    }

    /* Hover effect on rows */
    #affiliate-metrics-table tbody tr:hover {
        background-color: #f8f8f8;
    }
</style>


<div class="col-12 mt-4">
    <div class="card">
        <div class="card-body">
            <canvas id="earningsClicksChart"></canvas>
        </div>
    </div>
</div>




<!-- Script for Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> <!-- Chart.js Data Labels Plugin -->

<script>
    // Live data from the server, injected into the JS from the Blade view
    const metricsData = @json($metrics); // Passing the live data to JS

    // Prepare data for the line chart
    const chartData = {
        labels: metricsData.map(item => item.date), // X-axis (Date)
        datasets: [
            {
                label: 'Total Earnings ($)',
                data: metricsData.map(item => item.total_earnings), // Y-axis (Total Earnings)
                borderColor: 'rgba(75, 192, 192, 1)', // Line color for earnings (light green)
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Fill color under the curve
                borderWidth: 3,
                tension: 0.4, // Smooth curve between the points
                fill: true, // Fill the area under the line
                pointRadius: 6, // Slightly larger points
                pointBackgroundColor: 'rgba(75, 192, 192, 1)', // Color of the points
                yAxisID: 'y1', // Link to the first y-axis (for Earnings)
                datalabels: {
                    align: 'top', // Position of the data labels
                    color: 'rgba(75, 192, 192, 1)', // Label color for earnings
                    font: {
                        weight: 'bold', // Make the font bold for clarity
                    },
                    formatter: (value) => `$${value.toLocaleString()}`, // Format value as currency
                    offset: 8, // Position data labels slightly above the points
                }
            },
            {
                label: 'Total Clicks',
                data: metricsData.map(item => item.clicks), // Y-axis (Total Clicks)
                borderColor: 'rgba(255, 159, 64, 1)', // Line color for clicks (orange)
                backgroundColor: 'rgba(255, 159, 64, 0.2)', // Fill color under the curve
                borderWidth: 3,
                tension: 0.4, // Smooth curve for clicks
                fill: false, // No fill under the clicks line
                pointRadius: 6, // Slightly larger points
                pointBackgroundColor: 'rgba(255, 159, 64, 1)', // Color of the points
                yAxisID: 'y2', // Link to the second y-axis (for Clicks)
                datalabels: {
                    align: 'bottom', // Position of the data labels
                    color: 'rgba(255, 159, 64, 1)', // Label color for clicks
                    font: {
                        weight: 'bold', // Make the font bold for clarity
                    },
                    formatter: (value) => value.toLocaleString(), // Display the number of clicks
                    offset: -8, // Position data labels slightly below the points
                }
            }
        ]
    };

    // Chart Options with Dual Y-Axes
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top', // Position of the legend at the top
                labels: {
                    font: {
                        size: 14, // Slightly larger font for legend
                    }
                }
            },
            tooltip: {
                mode: 'index', // Tooltip for multiple datasets
                intersect: false, // Allow tooltip for both lines to appear simultaneously
                callbacks: {
                    label: function(tooltipItem) {
                        const label = tooltipItem.dataset.label || '';
                        const value = tooltipItem.raw.toLocaleString();
                        if (tooltipItem.datasetIndex === 0) {
                            // Earnings tooltip
                            return label + ': $' + value;
                        } else {
                            // Clicks tooltip
                            return label + ': ' + value + ' Clicks';
                        }
                    }
                }
            },
            datalabels: {
                display: true, // Enable data labels
                color: 'black', // Make the data labels black for better visibility
                font: {
                    size: 12, // Set font size for the data labels
                    weight: 'bold', // Bold font for better readability
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Date', // X-axis label
                    font: {
                        size: 14,
                    }
                },
                type: 'category', // Type 'category' for date strings as categories
                labels: metricsData.map(item => item.date), // Use the dates as labels
                ticks: {
                    autoSkip: true, // Automatically skips dates if there are too many
                    maxRotation: 0, // Keep the date labels horizontal (not rotated)
                    minRotation: 0, // Ensure no rotation
                    font: {
                        size: 12,
                    }
                }
            },
            y1: { // Left Y-axis for Earnings
                title: {
                    display: true,
                    text: 'Total Earnings ($)', // Y-axis label for Earnings
                    font: {
                        size: 14,
                    }
                },
                position: 'left', // Position of the first Y-axis
                beginAtZero: true, // Start the Y-axis at zero
                ticks: {
                    font: {
                        size: 12,
                    }
                },
                grid: {
                    drawOnChartArea: false, // Optional: Hide gridlines for this axis
                }
            },
            y2: { // Right Y-axis for Clicks
                title: {
                    display: true,
                    text: 'Total Clicks', // Y-axis label for Clicks
                    font: {
                        size: 14,
                    }
                },
                position: 'right', // Position of the second Y-axis
                beginAtZero: true, // Start the Y-axis at zero
                ticks: {
                    font: {
                        size: 12,
                    }
                },
                grid: {
                    drawOnChartArea: false, // Optional: Hide gridlines for this axis
                }
            }
        }
    };

    // Render the Line Chart
    const ctx = document.getElementById('earningsClicksChart').getContext('2d');
    new Chart(ctx, {
        type: 'line', // Use a line chart
        data: chartData,
        options: chartOptions
    });
</script>

<style>
    /* Optional: Adjust the size of the chart */
    #earningsClicksChart {
        height: 400px; /* Set height for the chart */
        width: 100%;   /* Ensure the chart fills the available width */
    }

    /* Optional: Customize the point hover effect */
    .chartjs-tooltip {
        background-color: rgba(0, 0, 0, 0.7); /* Dark background for tooltips */
        color: #fff; /* White text */
        border-radius: 4px;
        padding: 10px;
        font-size: 14px;
    }
</style>






<div class="row match-height">
    <div class="col-12">
        <h2 class="mb-3">Referrals</h2>
    </div>
    <div class="container">
        <h2>Welcome, Affiliate {{ $user->name }}</h2>
        <h3>Your Referrals</h3>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered On</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referrals as $referral)
                    <tr>
                        <td>{{ $referral->name }}</td>
                        <td>{{ $referral->email }}</td>
                        <td>{{ $referral->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>






                 

                 

 <div class="row match-height">


                    </div>
                </section>
                <!-- Dashboard Ecommerce ends -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    @endsection