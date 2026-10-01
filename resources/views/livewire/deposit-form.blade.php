<div class="app-content content">
     <div style="margin: 0 auto;"> 
        <h1 class="text-center mb-1">Deposit Funds</h1>

        @if(session('banks'))
    <div class="alert alert-success text-center mt-3">{{ session('banks') }}</div>
@endif

        <form wire:submit.prevent="submit" enctype="multipart/form-data">

            <!-- Step 1: Select Payment Method -->
            <div class="step" id="step-1" style="{{ $step === 1 ? 'display:block' : 'display:none' }}">
                <div class="form-group">
                    <label for="payment_method" class="form-label">Choose Payment Method</label>
                    <select wire:model="paymentMethodId" id="payment_method" class="form-control" required>
                        <option value="">-- Select Payment Method --</option>
                         @foreach ($paymentMethods as $index => $paymentMethod)
 
    <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
    @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-primary w-100 mt-4" wire:click="nextStep">Continue</button>
            </div>

            <!-- Step 2: Conditional Fields Based on Payment Method and Enter Amount -->
            <div class="step" id="step-2" style="{{ $step === 2 ? 'display:block' : 'display:none' }}">
                <div class="conditional-fields">



                 
                    @if($paymentMethodId == 1) <!-- Bank transfer -->
                        <div class="form-group">
                            
                            <label for="country">Select Country</label>

                            <select wire:model="countryId" id="country" class="form-control" required>
                                <option value="">-- Select Country --</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        @elseif($paymentMethodId == 2)


                                               <button type="button" id="payButton" class="btn btn-primary" onclick="window.location.href='/card';">Card Payment</button>



<style>
    .btn-mini {
    font-size: 0.75rem;  /* smaller font size */
    padding: 4px 8px;    /* reduced padding */
}

</style>





                    @elseif($paymentMethodId == 3) <!-- Crypto -->
                        <div class="form-group">
                            <label for="crypto_currency">Choose Cryptocurrency</label>
                            <select wire:model="cryptoCurrency" id="crypto_currency" class="form-control" required>
        <option value="">-- Select Cryptocurrency --</option>
        @foreach ($cryptoCurrencies as $index => $crypto)
            <option value="{{ $crypto }}">
                {{ $crypto }} 
                @if ($index == 2) <!-- Check if the cryptocurrency is at index 2 (USDT) -->
                    (ERC20)
                @endif
            </option>
        @endforeach
    </select>
                        </div>
                    @endif

                    @if ($paymentMethodId != 2) 
    <div class="form-group">
        <label for="amount">Enter Amount</label>
        <input type="number" wire:model="amount" id="amount" class="form-control" required min="1000">
        <small class="form-text text-muted">Minimum $1,000</small>
    </div>
@endif

                    @if($this->convertedAmount && $paymentMethodId === '3')
                        <div class="form-group">
                            <label>Converted Amount</label>
                            <p>{{ number_format((float)$this->convertedAmount, 6) }} {{ $cryptoCurrency }}</p>
                        </div>
                    @endif
                </div>

           

                @if($paymentMethodId == 1)
<!-- Loading Spinner or Message -->
<div wire:loading wire:target="nextStep" class="text-center" style="padding: 25px 20px; color: black; border-radius: 15px; box-shadow: 0 6px 15px; max-width: 100%; margin: 0 auto; background-color: white;">

    <!-- Spinner with message -->
    <div class="spinner-border" role="status" style="width: 2.5rem; height: 2.5rem; border-width: 5px; border-top-color:red; animation: spin 1s linear infinite;">
    </div>

    <!-- Notice message -->
    <small class="form-text text-muted" 
       style="display: block; color: black; padding: 12px; border: 1px solid #000000; border-radius: 8px; font-size: 14px; margin-top: 15px; font-weight: 1000;">
      Generating account details... 
      This might take a moment.
    </small>

    <!-- Second notice message -->
    <small class="form-text text-muted" 
       style="display: block; color: black; padding: 12px; border: 1px solid #000000; border-radius: 8px; font-size: 14px; margin-top: 12px; font-weight: 1000;">
     The payment details will be automatically sent to your email once they are generated.
    </small>

</div>

@endif

<!-- Add custom media queries for responsiveness -->
<style>
    /* For small screens (mobile devices) */
    @media (max-width: 576px) {
        .spinner-border {
            width: 2rem;  /* Make the spinner smaller for mobile */
            height: 2rem;
        }
        .form-text {
            font-size: 12px;  /* Smaller font size for small screens */
            padding: 8px;
        }
        .text-center {
            padding: 15px 10px;
        }
        .spinner-border {
            width: 2rem; /* Adjust spinner size */
        }
    }

    /* For medium screens (tablets) */
    @media (min-width: 577px) and (max-width: 992px) {
        .spinner-border {
            width: 2.5rem;
            height: 2.5rem;
        }
        .form-text {
            font-size: 13px;
            padding: 10px;
        }
    }

    /* For larger screens (desktops and beyond) */
    @media (min-width: 993px) {
        .spinner-border {
            width: 2.5rem;
            height: 2.5rem;
        }
        .form-text {
            font-size: 14px;
            padding: 12px;
        }
    }
</style>


              


                @if($paymentMethodId == 3) 
                <div wire:loading class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <!-- <span class="sr-only">..getting crypto amount</span> -->
                        <p></p>
                    </div>
                  
                </div>


                @endif

                <div class="button-group">
                    
                          @if($paymentMethodId != 2)
                    <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                    <button type="button" class="btn btn-primary" wire:click="nextStep">Continue</button>
                   @endif
<style>
    /* General button group layout */
    .button-group {
        display: flex;
        justify-content: space-between;
        gap: 10px;  /* Add some space between the buttons */
    }

    /* For small screens (mobile devices) */
    @media (max-width: 576px) {
        .button-group {
            flex-direction: column; /* Stack buttons vertically on mobile */
            align-items: center;     /* Center buttons horizontally */
        }

        .button-group .btn {
            width: 100%;  /* Make buttons take up full width on mobile */
        }

        .button-group .btn:last-child {
            margin-top: 10px; /* Add space only for the second button */
        }

        /* Ensure the "Continue" button comes first on mobile */
        .button-group .btn-primary {
            order: -1;  /* Place the "Continue" button on top */
        }
    }

    /* For larger screens (tablets and above) */
    @media (min-width: 577px) {
        .button-group {
            flex-direction: row; /* Align buttons horizontally on larger screens */
            justify-content: flex-start;
        }

        .button-group .btn {
            width: auto;  /* Buttons should take their natural width on larger screens */
        }
    }
</style>

                </div>
            </div>

            <!-- Step 3: Display Information and Upload Receipt -->
            <div class="step" id="step-3" style="{{ $step === 3 ? 'display:block' : 'display:none' }}">
                @if($paymentMethodId == 1 && $countryId)
                    <!-- Display Bank Account details -->
                    <div class="form-group" id="bankDetails">
                        <!-- <h5>Bank Account Details</h5> -->
                        <p>Your payment details have been successfully generated. You will receive an email with the details shortly</p>
                        <!-- <p>Bank Name: <span>{{ $this->getBankDetails($countryId)->name ?? 'N/A' }}</span></p> -->
                        <!-- <p>Account Number: <span>{{ $this->getBankDetails($countryId)->account_number ?? 'N/A' }}</span></p> -->
                    </div>
                @elseif($paymentMethodId == 3 &&  $this->cryptoAddress)
  <!-- Display Crypto Address -->
<div class="form-group" id="cryptoAddressGroup" style="max-width: 800px; margin: 0 auto; padding: 30px; background-color: #ffffff; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); font-family: 'Roboto', sans-serif;">
    
    <!-- Display Cryptocurrency and Amount Section -->
    <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 25px;">
        <!-- Amount and Cryptocurrency -->
        <p style="font-size: 1.5rem; font-weight: 700; color: #2c3e50; margin-bottom: 5px;">
            <strong>
                {{ number_format((float)$this->convertedAmount, 6) }} 
                {{ $this->cryptoCurrency }} 
                @if($this->cryptoCurrency == 'USDT') 
                    <span style="font-size: 1rem; color: #7f8c8d; font-weight: 500;">(ERC20)</span>
                @endif
            </strong>
        </p>
    </div>

    <!-- Display Crypto Address Box -->
    <div style="display: flex; justify-content: space-between; align-items: center; background-color: #f3f4f6; padding: 8px 12px; border-radius: 8px; border: 1px solid #dfe1e5; position: relative; max-width: 90%; margin: 0 auto; flex-wrap: nowrap;">
        <p style="font-size: 0.9rem; color: #34495e; font-weight: 500; word-wrap: break-word; margin: 0; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ $this->cryptoAddress ?? 'N/A' }}
        </p>
        
        <!-- Copy Button -->
        <button type="button" id="copyButton" onclick="copyAddress()" style="background-color: #e67e22; border: none; padding: 6px 12px; color: white; font-size: 0.8rem; border-radius: 5px; cursor: pointer; margin-left: 10px; transition: background-color 0.3s; flex-shrink: 0;">
            Copy
        </button>
    </div>

    <!-- QR Code Section -->
    <div style="display: flex; justify-content: center; margin-top: 30px; border-radius: 10px; overflow: hidden;">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($this->cryptoAddress) }}" alt="QR Code" style="width: 150px; height: 150px; object-fit: cover; transition: transform 0.3s ease-in-out;">
    </div>

    <!-- Hover effect on QR code -->
    <style>
        #cryptoAddressGroup img:hover {
            /* transform: scale(1.1); */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        #copyButton:hover {
            background-color: #d35400;
        }

        /* Media Queries for responsiveness */
        @media (max-width: 768px) {
            #cryptoAddressGroup {
                padding: 20px;
            }
            #cryptoAddressGroup p {
                font-size: 0.9rem;
            }
            #copyButton {
                font-size: 0.7rem;
                padding: 5px 10px;
            }
            #cryptoAddressGroup img {
                width: 120px;
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            #cryptoAddressGroup {
                padding: 15px;
            }
            #cryptoAddressGroup p {
                font-size: 0.8rem;
                word-wrap: break-word;
            }
            #cryptoAddressGroup img {
                width: 100px;
                height: 100px;
            }
        }
    </style>

    <!-- Additional Instructions -->
    <div style="margin-top: 25px; text-align: center; color: #95a5a6; font-size: 0.8rem; line-height: 1.5;">
        <small>Scan the QR code or use the payment address above to send your payment. The payment will be automatically processed. If you have any inquiries, our support team is available to assist you.</small>
    </div>
</div>

<!-- JavaScript for Copy to Clipboard -->
<script>
    function copyAddress() {
        // Get the text field
        var addressText = document.querySelector('#cryptoAddressGroup p').textContent;

        // Create a temporary textarea to copy the text to clipboard
        var tempTextarea = document.createElement('textarea');
        tempTextarea.value = addressText;
        document.body.appendChild(tempTextarea);
        tempTextarea.select();
        document.execCommand('copy');
        document.body.removeChild(tempTextarea);

        // Optionally, show a confirmation alert
        alert('Address copied to clipboard!');
    }
</script>

<!-- JavaScript for Copy to Clipboard -->
<script>
    function copyAddress() {
        // Get the text field
        var addressText = document.querySelector('#cryptoAddressGroup p').textContent;

        // Create a temporary textarea to copy the text to clipboard
        var tempTextarea = document.createElement('textarea');
        tempTextarea.value = addressText;
        document.body.appendChild(tempTextarea);
        tempTextarea.select();
        document.execCommand('copy');
        document.body.removeChild(tempTextarea);

        // Optionally, show a confirmation alert
        alert('Address copied to clipboard!');
    }
</script>


                @endif
                @if($paymentMethodId == 1)
                <small class="form-text text-muted" 
       style="display: block;  color: black; padding: 10px; border: 1px solid #ffeeba; border-radius: 5px; font-size: 14px;">
    <strong>Notice:</strong> Payment should be made within 24hrs, or you will have to regenerate new payment details.
</small>
@endif
    
                <div class="button-group">
                    <button type="button" class="btn btn-secondary" wire:click="previousStep">Back</button>
                    @if($paymentMethodId == 3)
                    <button type="submit" class="btn btn-primary">Confirm Deposit</button>
                    @endif
                    
                  
<style>
    /* General button group layout */
    .button-group {
        display: flex;
        justify-content: space-between;
        gap: 10px;  /* Add some space between the buttons */
    }

    /* For small screens (mobile devices) */
    @media (max-width: 576px) {
        .button-group {
            flex-direction: column; /* Stack buttons vertically on mobile */
            align-items: center;     /* Center buttons horizontally */
        }

        .button-group .btn {
            width: 100%;  /* Make buttons take up full width on mobile */
        }

        .button-group .btn:last-child {
            margin-top: 10px; /* Add space only for the second button */
        }

        /* Ensure the "Continue" button comes first on mobile */
        .button-group .btn-primary {
            order: -1;  /* Place the "Continue" button on top */
        }
    }

    /* For larger screens (tablets and above) */
    @media (min-width: 577px) {
        .button-group {
            flex-direction: row; /* Align buttons horizontally on larger screens */
            justify-content: flex-start;
        }

        .button-group .btn {
            width: auto;  /* Buttons should take their natural width on larger screens */
        }
    }
</style>

                </div>
            </div>
        </form>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success text-center mt-3">{{ session('success') }}</div>
        @endif

    

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>



<!-- Place this inside your <script> tag after the DOM is ready -->
<script>
// Ensure this function is available globally after the DOM content is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Attach the copyAddress function to the button after the page has loaded
    const copyButton = document.getElementById('copyButton');
    if (copyButton) {
        copyButton.addEventListener('click', function() {
            copyAddress();
        });
    }
});

// Function to copy the address
function copyAddress() {
    // Get the text content (Crypto address) from the paragraph
    var addressText = document.querySelector('#cryptoAddressGroup p').textContent;

    // Check if the Clipboard API is available
    if (navigator.clipboard && window.isSecureContext) {
        // Use the Clipboard API for modern browsers
        navigator.clipboard.writeText(addressText).then(function() {
            // Successfully copied to clipboard
            alert('Address copied to clipboard!');
        }).catch(function(error) {
            // If there's an error during copying
            console.error('Failed to copy address: ', error);
            alert('Failed to copy address. Please try again.');
        });
    } else {
        // Fallback to document.execCommand for older browsers
        var tempTextarea = document.createElement('textarea');
        tempTextarea.value = addressText;
        document.body.appendChild(tempTextarea);
        tempTextarea.select();
        var successful = document.execCommand('copy');
        document.body.removeChild(tempTextarea);

        // Alert after copying
        if (successful) {
            alert('Address copied to clipboard!');
        } else {
            alert('Failed to copy address. Please try again.');
        }
    }
}
</script>

<!-- Add JavaScript to handle showLoading and hideLoading -->
@push('scripts')
    <script>
        Livewire.on('showLoading', () => {
            // Show spinner and loading message
            document.querySelector('.spinner-border').style.display = 'block';
            document.querySelector('p').style.display = 'block';
        });

        Livewire.on('hideLoading', () => {
            // Hide spinner and loading message
            document.querySelector('.spinner-border').style.display = 'none';
            document.querySelector('p').style.display = 'none';
        });
    </script>
@endpush


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Handle the loading process for step 2
    Livewire.on('showLoading', () => {
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing your request...',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });

    // Handle hiding the loading spinner
    Livewire.on('hideLoading', () => {
        Swal.close();
    });

    // Success message when form is submitted
    Livewire.on('successMessage', (message) => {
        Swal.fire({
            title: 'Success!',
            text: message,
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });

    // Error message
    Livewire.on('errorMessage', (message) => {
        Swal.fire({
            title: 'Error!',
            text: message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
</script>
@endpush
