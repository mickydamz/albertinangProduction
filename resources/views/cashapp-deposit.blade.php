@extends('layouts.app')

@section('content')
    <!-- Begin app-content wrapper for Vuexy layout -->
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <!-- Page header content (optional) -->
            </div>

            <div class="content-body">
                <div class="row mt-5 mb-5 justify-content-center">
                    <div class="col-md-4">
                        <!-- Display success or error messages from the session -->
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- CashApp Deposit Form -->
                        <form action="/cashapp-deposit" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="unit_amount" style="font-size: 14px; font-weight: bold; color: #333;">Enter the amount (in USD):</label>
                                <input type="number" id="unit_amount" name="unit_amount" class="form-control" min="1" step="0.01" required style="font-size: 16px; border-radius: 25px; border: 2px solid #E0E0E0; padding: 10px 20px; box-shadow: none; transition: all 0.3s ease-in-out;">
                            </div>

                            <!-- Deposit Limit Instruction -->
                            <div class="form-group mb-4">
                                <small class="form-text text-muted" style="font-size: 12px; color: #666;">
                                    Maximum deposit: $1500. For transactions higher than $1500, please use bank transfer or crypto.
                                </small>
                            </div>

                            <!-- CashApp Pay Button -->
                            <div class="form-group">
                                <button type="submit" class="btn w-100" style="background-color: #00BFAE; color: white; font-weight: bold; border: none; padding: 15px; font-size: 18px; border-radius: 25px; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0, 191, 174, 0.3); transition: all 0.3s ease-in-out;">
                                    Pay with CashApp
                                </button>
                            </div>

                            <!-- CashApp Address Instructions -->
                            <div class="form-group mb-3">
                                <label for="cashapp_email" style="font-size: 14px; font-weight: bold; color: #333;">Copy the CashApp address below to deposit into your account:</label>
                                <input type="text" id="cashapp_email" class="form-control" value="cashapp@example.com" readonly style="font-size: 16px; border-radius: 25px; border: 2px solid #E0E0E0; padding: 10px 20px; background-color: #F9F9F9; box-shadow: none;">
                            </div>

                            <!-- CashApp Proceed Button -->
                            <div class="form-group">
                                <a href="https://cash.app" target="_blank" class="btn w-100" style="border: 2px solid #00BFAE; color: #00BFAE; font-weight: bold; padding: 15px; font-size: 18px; border-radius: 25px; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0, 191, 174, 0.3); transition: all 0.3s ease-in-out;">
                                    Proceed to CashApp
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End app-content wrapper -->
@endsection
