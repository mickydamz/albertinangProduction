<div>
    <!-- Step 1: Payment Method Selection -->
    <div class="form-group">
        <label for="payment_method">Select Payment Method</label>
        <select wire:model="selectedPaymentMethod" class="form-control">
            <option value="">-- Select Payment Method --</option>
            @foreach ($paymentMethods as $paymentMethod)
                <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Step 2: Country Selection (shown if payment method is Bank) -->
    @if (!empty($countries))
        <div class="form-group mt-3">
            <label for="country">Select Country</label>
            <select wire:model="selectedCountry" class="form-control">
                <option value="">-- Select Country --</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Step 3: Bank Account Details (shown based on country selection) -->
    @if (!empty($bankAccounts))
        <div class="form-group mt-3">
            <h5>Bank Account Details</h5>
            <ul>
                @foreach ($bankAccounts as $account)
                    <li>{{ $account->bank_name }} - Account Number: {{ $account->account_number }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
