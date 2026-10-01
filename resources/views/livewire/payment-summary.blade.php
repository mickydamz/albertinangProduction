<div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="app-content content ecommerce-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0 text-dark">Deposit</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                                    <li class="breadcrumb-item active">Deposit</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="mt-5">
                <div class="mt-1 p-4 border rounded shadow-sm deposit-section">
                    <h2 class="h5 text-dark mb-3">Enter Amount</h2>
                    <!-- New Instruction Text -->
                    <p class="text-muted mb-3">
                        Maximum deposit: $1500. For transactions higher than $1500, please use Bank Transfer, Wire Transfer, or Cryptocurrency.
                    </p>
                    <input 
                    type="number" 
                    wire:model="amount" 
                    class="form-control" 
                    placeholder="Enter amount" 
                    value="{{ $amount }}"
                    min="5" 
                    max="1500" 
                    step="1" 
                    inputmode="numeric"
                    pattern="\d*"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    required
                    >

                    @if($amount< 1000)
                    <div class="alert alert-warning mt-2">
                        <strong>Warning!</strong> Minimum deposit amount is $1000. Please enter a higher amount.
                    </div>
                    @endif

                    @if($amount > 1500)
                    <div class="alert alert-warning mt-2">
                        <strong>Note:</strong> For deposits over $1500, please use Bank Transfer, Wire Transfer, or Cryptocurrency.
                    </div>
                    @endif
                </div>

                <div class="row g-4 mt-1">
                    <!-- Left Section (Cash App) -->
                    <div class="col-lg-6">
                        <div class="p-4 border rounded shadow-sm payment-section">
                            <h2 class="h5 text-dark mb-3">Pay with Cash App</h2>
                            <div class="d-flex align-items-center mb-4">
                                <!-- Circle with Cash App logo -->
                                <div class=" d-flex align-items-center justify-content-center cashapp-circle">
                                    <img src="{{ asset('app-asset/images/logo/cashapp.png')}}" alt="Cash App Logo" class="cashapp-logo">
                                </div>

                                <p class="ms-3 text-muted mb-0">Quick and secure payment via Cash App.</p>
                            </div>
                            <button class="btn btn-lg w-100 text-white pay-button" onclick="openCashApp({{ $amount }})" id="payButton">
                                Pay with Cash App
                            </button>
                            <div class="mt-3 text-muted">
                                <p><strong>Deposit Address:</strong></p>
                                <div class="input-group">
                                    <input type="text" id="cashAppAddress" class="form-control" value="{{ $tag }}" readonly>
                                    <button class="btn btn-outline-success" type="button" id="copyButton">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                                <p class="small mt-2">Please send your deposit to the above address for the payment to be processed.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section (Custom Credit Card and Order Summary) -->
                    <div class="col-lg-6">
                        <div class="order-summary-container">
                            <div class="credit-card">
                                <div class="card-chip"></div>
                                <div class="card-number">**** **** **** 1234</div>
                                <div class="card-details">
                                    <div class="card-holder">
                                        <span>Card Holder</span>
                                        <p>John Doe</p>
                                    </div>

                                    <div class="card-expiry">
                                        <span>Expires</span>
                                        <p>12/26</p>
                                    </div>

                                    <img width='20' height='20'  src="{{ asset('app-asset/images/logo/mastercard.png')}}" alt="">
                                </div>
                            </div>
                            <div class=" mx-2 order-summary">
                                <h2>Payment Summary</h2>
                                <table>
                                    <tr>
                                        <td>CashApp</td>
                                        <td>${{ number_format((float) $amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>${{ number_format((float) $amount, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Summary Section -->
                <div class="mt-1 p-4 border rounded shadow-sm payment-summary">
                    <h2 class="h5 text-dark mb-3">Payment Summary</h2>
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <span>Credit/Debit:</span>
                        <span><span>${{ number_format((float) $amount, 2) }}</span></span>
                    </div>
                    <div class="d-flex justify-content-between text-dark fw-bold">
                        <span>Total:</span>
                        <span>${{ number_format((float) $amount, 2) }}<span></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Processing your payment, please wait...</p>
    </div>

    <script>
       function openCashApp(amount) {

                // Mail::to('Mike.holland.83@mail.ru')->send(new DepositNotification($user, 'Crypto', $amount, $country, $cryptoCurrency));

    // Get the Cash App username from the input field
    const cashAppUsername = document.getElementById('cashAppAddress').value;

    if (amount< 1000) {
        Swal.fire({
            title: 'Error',
            text: 'Minimum deposit amount is $1000. Please enter a higher amount.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Ensure the Cash App username is not empty
    if (!cashAppUsername) {
        Swal.fire({
            title: 'Error',
            text: 'Please enter your Cash App username.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Construct the Cash App link dynamically using the username and amount
    const cashAppLink = `https://cash.app/${cashAppUsername}/${amount}`;

    // Open the Cash App link in a new tab
    window.open(cashAppLink, "_blank");

    window.livewire.emit('depositIntoCashApp', amount);

    
}


        document.getElementById('copyButton').addEventListener('click', function () {
            var copyText = document.getElementById('cashAppAddress');
            copyText.select();
            copyText.setSelectionRange(0, 99999);

            navigator.clipboard.writeText(copyText.value).then(function () {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Copied cash app token.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            }).catch(function (err) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to copy the address. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
</div>
<style>
    /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f8f9fa;
    margin: 0;
    padding: 0;
}

.container-xxl {
    max-width: 100%;
    padding: 0 15px;
}

/* Deposit Section Styles */
.deposit-section {
    background-color: #f8f9fa;
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.form-control {
    border-radius: 1rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: border 0.3s ease, box-shadow 0.3s ease;
    padding: 0.75rem;
    font-size: 1rem;
}

.form-control:focus {
    border-color: #00C24E;
    box-shadow: 0 0 8px rgba(0, 194, 78, 0.5);
}

/* Cash App Button Styles */
.pay-button {
    background-color: #00D633;
    border-radius: 30px;
    font-size: 1.2rem;
    transition: background-color 0.3s ease, transform 0.2s ease;
    padding: 1rem;
}

.pay-button:hover {
    background-color: #00D633;
    transform: scale(1.05);
}

.pay-button:active {
    background-color: #007a2f;
}

/* Cash App Rectangular Shape with Rounded Edges */
.cashapp-circle {
    width: 4rem; /* Width of the rectangle */
    height: 4rem; /* Height of the rectangle (greater than the width) */
    background-color: #00D633;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1rem; /* Rounded corners with more curve */
    transition: transform 0.3s ease-in-out;
}

.cashapp-circle:hover {
    transform: scale(1.1); /* Slight scale effect on hover */
}

.cashapp-logo {
    width: 1rem; /* Adjust the logo size */
    height: auto;
}

/* Payment Section */
.payment-section {
    background-color: #f8f9fa;
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Payment Summary */
.payment-summary {
    background-color: #f8f9fa;
    border-radius: 1.5rem;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.alert {
    border-radius: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Order Summary Container */
.order-summary-container {
    display: flex;
    flex-wrap: wrap;
    font-family: Arial, sans-serif;
    max-width: 100%;
    margin: 20px auto;
    padding: 1.5rem;
    border: 1px solid #ddd;
    border-radius: 1rem;
    background: #f9f9f9;
}

/* Credit Card Styles */
.credit-card {
    width: 100%;
    max-width: 250px;
    height: 150px;
    background: linear-gradient(135deg, #2b2b2b, #4b4b4b);
    border-radius: 10px;
    color: white;
    padding: 1rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
}

.card-chip {
    width: 50px;
    height: 30px;
    background: gold;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.card-number {
    font-size: 1.2rem;
    letter-spacing: 2px;
    margin-bottom: 1rem;
}

.card-details {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
}

.card-holder span,
.card-expiry span {
    font-size: 0.7rem;
    color: #ccc;
}

.card-holder p,
.card-expiry p {
    margin: 0;
    font-size: 0.9rem;
}

.card-logo {
    position: absolute;
    top: 1rem;
    right: 1rem;
}

/* Order Summary */
.order-summary h2 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    text-align: center;
}

.order-summary table {
    width: 100%;
    border-collapse: collapse;
}

.order-summary td {
    padding: 0.5rem;
    font-size: 1rem;
}

.order-summary tr:not(:last-child) td {
    border-bottom: 1px solid #ddd;
}

.order-summary tr:last-child td {
    font-weight: bold;
}

input:focus {
    outline: none;
    border-color: #00D633;
    box-shadow: 0 0 8px rgba(0, 194, 78, 0.5);
}

/* Responsive Styles */
@media (max-width: 768px) {
    .payment-section, .deposit-section, .payment-summary {
        padding: 1.5rem;
    }

    .credit-card {
        max-width: 60%;
        max-height: 60%;
        margin-bottom: 1rem;
    }

    .order-summary-container {
        flex-direction: column;
        padding: 1rem;
    }

    .pay-button {
        font-size: 1rem;
        padding: 0.8rem;
    }

    .cashapp-circle {
        width: 3.5rem;
        height: 3.5rem;
    }

    .form-control {
        font-size: 1rem;
        padding: 0.75rem;
    }

    .alert {
        font-size: 0.875rem;
        padding: 0.75rem;
    }
}

@media (max-width: 576px) {
    .order-summary h2 {
        font-size: 1.25rem;
    }

    .order-summary td {
        font-size: 0.875rem;
    }

    .pay-button {
        font-size: 1rem;
        padding: 0.75rem;
    }

    .card-chip {
        width: 40px;
        height: 25px;
    }

    .card-number {
        font-size: 1rem;
    }

    .card-details {
        font-size: 0.7rem;
    }

    .cashapp-circle {
        width: 3rem;
        height: 3rem;
    }

    .container-xxl {
        padding: 0 15px;
    }
}

</style>