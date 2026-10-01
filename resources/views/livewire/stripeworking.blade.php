<div class="app-content content">
    <div style="margin: 0 auto;">
        <h1 class="text-center mb-1">Deposit Funds</h1>

        <form wire:submit.prevent="submit" enctype="multipart/form-data">
            <!-- Step 2: Enter Amount -->
            <div class="step" id="step-2">
                <div class="form-group">
                    <label for="amount">Enter Amount</label>
                    <input type="number" wire:model="amount" id="amount" class="form-control" required min="1000">
                    <small class="form-text text-muted">Minimum $1,000</small>
                </div>

                <!-- Stripe Card Element -->
                <div id="card-element"></div>
                <div id="card-errors" role="alert"></div>

                <button type="button" id="payButton" class="btn btn-primary">Pay with Stripe</button>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success text-center mt-3">{{ session('success') }}</div>
            @endif
        </form>
    </div>
</div>

<!-- Stripe Scripts -->
<script src="https://js.stripe.com/v3/"></script>

<script>
    // Initialize Stripe
    var stripe = Stripe('pk_test_51Qe3vmGGQMvd2mOB3GG4LcPOiCbTDaBwkppi5WbWEIRMMSxEYlmZpzpEChavEqWS69aukURV1WzhW56lOIWngGX600qwnawvOf'); // Replace with your publishable key
    var elements = stripe.elements();

    // Create a Card Element
    var card = elements.create('card');
    card.mount('#card-element');

    // Add event listener to the "Pay with Stripe" button
    document.getElementById("payButton").addEventListener("click", payWithStripe);

    // Handle Stripe payment
    function payWithStripe() {
        var amount = document.querySelector('#amount').value;

        // Safely retrieve the CSRF token from the meta tag
        var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

        if (!csrfToken) {
            alert("CSRF token not found.");
            return;
        }

        // Create the payment intent from your server-side
        fetch('/create-payment-intent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.clientSecret) {
                stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: { name: 'Customer' }
                    }
                }).then(function(result) {
                    if (result.error) {
                        alert(result.error.message);
                    } else {
                        if (result.paymentIntent.status === 'succeeded') {
                            alert('Payment successful!');
                            // Optionally, call verifyPayment or other functions as needed
                        }
                    }
                });
            }
        });
    }
</script>

<!-- CSRF Token in meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">
'