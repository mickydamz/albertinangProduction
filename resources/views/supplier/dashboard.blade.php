@extends('layouts.supplierlayout')

@section('content')
    <!-- BEGIN: Content-->
    <div class="app-content content">
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
                                    <h3 class="mb-75 mt-2 pt-50">
                                        <a href="page-account-settings-account" class="btn btn-primary">View Profile</a>
                                    </h3>
                                    <img src="{{ asset('app-assets/images/illustration/badge.svg') }}" class="congratulation-medal" alt="Medal Pic" />
                                </div>
                            </div>
                        </div>

                        <!-- Account Balance Card -->
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="card earnings-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h4 class="card-title mb-1">Account Balance</h4>
                                            <div class="font-small-2">Total</div>
                                            <h5 class="mb-1">${{ number_format(auth()->user()->account_balance, 2) }}</h5>
                                            <p class="card-text text-muted font-small-2">
                                                <span class="fw-bolder">Account balance in USD</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                          <!-- Account Balance Card -->
                          <div class="col-xl-4 col-md-6 col-12">
                            <div class="card earnings-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h4 class="card-title mb-1">Total Products</h4>
                                            <div class="font-small-2">Total</div>
                                            <h5 class="mb-1">{{ number_format($totalProducts, 2) }}</h5>
                                            <p class="card-text text-muted font-small-2">
                                                <span class="fw-bolder">total products in stock</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row match-height">
                        <div class="col-lg-12 col-12">
                            <div class="row match-height">

                                <!-- Bar Chart - Orders -->
                                <div class="col-lg-6 col-md-3 col-6">
                                    <div class="card">
                                        <div class="card-body pb-50">
                                            <h6>Orders</h6>
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
                                            <h6>Public sales chart</h6>
                                            <h2 class="fw-bolder mb-1">$43M</h2>
                                            <div id="statistics-profit-chart"></div>
                                        </div>
                                    </div>
                                </div>
                                <!--/ Line Chart - Profit -->
                            </div>
                        </div>
                    </div>



                    <!-- Products Stats Row -->
                    <div class="row match-height">
                        <!-- Total Products Card -->
                         <!-- Account Balance Card -->
                         <div class="col-xl-4 col-md-6 col-12">
                            <div class="card earnings-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h4 class="card-title mb-1">Low Stock Products</h4>
                                            <div class="font-small-2">Total</div>
                                            <h5 class="mb-1">{{ number_format($lowStockProducts, 2) }}</h5>
                                            <p class="card-text text-muted font-small-2">
                                                <span class="fw-bolder">amount of low stock products</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Low Stock Products Card -->
                         <!-- Account Balance Card -->
                         <div class="col-xl-4 col-md-6 col-12">
                            <div class="card earnings-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h4 class="card-title mb-1">Recent Orders</h4>
                                            <div class="font-small-2">Total</div>
                                            <h5 class="mb-1">{{ number_format($recentOrdersCount, 2) }}</h5>
                                            <p class="card-text text-muted font-small-2">
                                                <span class="fw-bolder">recent orders</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                       

                        <!-- Monthly Sales Card -->
                        <div class="col-xl-4 col-md-6 col-12">
                            <div class="card earnings-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <h4 class="card-title mb-1">Monthly sales</h4>
                                            <div class="font-small-2">Total</div>
                                            <h5 class="mb-1">{{ number_format($monthlySales, 2) }}</h5>
                                            <p class="card-text text-muted font-small-2">
                                                <span class="fw-bolder">this month sales</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <!-- Paginated Products List -->
                    <div class="row mt-1">
                        <div class="col-12">
                            <h4>Your Products</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Stock</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->status ? 'Active' : 'Inactive' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $products->links() }}
                            </div>
                        </div>
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
