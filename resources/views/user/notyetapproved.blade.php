@extends('layouts.app')

@section('title', __("Documents are being reviewed"))

@section('content')
<div class="nk-content-body">
    <div class="nk-block wide-xs mx-auto">
        <div class="text-center">
            <em class="icon icon-circle icon-circle-xxl ni ni-check bg-success mt-md-4"></em>
            <div class="content">
                <span class="h5 fw-normal mb-3 mt-5 text-base d-block">{{ __("Congratulations!") }}</span>
                <h2 class="nk-block-title fw-normal">{{ __('Your Documents are being reviewed!', ['fullname' => $user->name]) }}</h2>
                <p class="caption-text w-max-350px mx-auto">{{ __("We understand that you have previously uploaded your documents. You will contacted when your identity has been verified.") }}</p>
            </div>
            <ul class="btn-group align-center justify-center gx-2 pt-5">
                
                <li><a href="{{ route('dashboard') }}" class="btn btn-lg btn-primary">Dashboard</a></li>
                
            </ul>
            <!--<ul class="btn-group-vertical align-center pt-4">-->
                
            <!--    <li><a href="{{ route('dashboard') }}" class="link link-primary">{{ __('Go to Dashboard') }}</a></li>-->
            <!--</ul>-->
        </div>
    </div>
</div>
@endsection
