<div class="row">
    @if(session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <p><strong>Oops! Something went wrong</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- User Information -->
    <div class="col-12 col-sm-6 mb-1">
        <label class="form-label" for="accountFirstName">First Name</label>
        <input type="text" class="form-control" id="accountFirstName" name="firstName" wire:model="name" placeholder="John" oninput="updateCompletion()" />
    </div>
    <div class="col-12 col-sm-6 mb-1">
        <label class="form-label" for="accountEmail">Email</label>
        <input type="email" class="form-control" id="accountEmail" name="email" wire:model="email" placeholder="Email" oninput="updateCompletion()" />
    </div>
    <div class="col-12 col-sm-6 mb-1">
        <label class="form-label" for="accountPhoneNumber">Phone Number</label>
        <input type="text" class="form-control account-number-mask" id="accountPhoneNumber" wire:model="phone_no" name="phoneNumber" placeholder="Phone Number" oninput="updateCompletion()" />
    </div>

    <!-- Shipping shipping_address Information -->
    <div class="col-12 col-sm-6 mb-1">
        <label class="form-label" for="accountAddress">Address</label>
        <input type="text" class="form-control" id="accountAddress" name="shipping_address" wire:model="shipping_address" placeholder="Your Address" oninput="updateCompletion()" />
    </div>
    <div class="col-12 col-sm-6 mb-1">
        <label class="form-label" for="accountState">City</label>
        <input type="text" class="form-control" id="accountState" name="city" wire:model="city" placeholder="city" oninput="updateCompletion()" />
    </div>
    <div class="col-12 col-sm-6 mb-1">
        <label class="form-label" for="accountZipCode">Postal Code</label>
        <input type="text" class="form-control account-zip-code" id="accountZipCode" name="postal_code" wire:model="postal_code" placeholder="Zip Code" maxlength="6" oninput="updateCompletion()" />
    </div>

    <!-- Country (Non-Editable) -->
    <div class="col-12 col-sm-6 mb-1">
        <label class="form-label" for="country">Country</label>
        <input type="text" class="form-control" id="country" name="country" wire:model="country" oninput="updateCompletion()" />
    </div>

    <!-- Submit and Reset Buttons -->
    <div class="col-12">
        <button type="submit" wire:click="update()" class="btn btn-primary mt-1 me-1">Save changes</button>
        <button type="reset" class="btn btn-outline-secondary mt-1">Discard</button>
    </div>

 
  
</div>

