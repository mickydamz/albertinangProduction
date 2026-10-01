@extends('layouts.app')

@section('title', __("Verify your ID"))

@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <!-- Header Section with Logout Button -->
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title text-primary float-start mb-0" style="color: #C68E17;">Verification</h2>
                    </div>
                </div>
            </div>
        </div>

<div class=" nk-content-body">
    <div class="nk-block-head nk-block-head-lg wide-xs mx-auto">
        <div class="nk-block-head-content text-center">
           
        </div>
    </div>

    <div class="nk-block wide-xs mx-auto">
        <div class="card card-bordered">
            <div class="card-inner card-inner-lg">
              
                
                
   @if (auth()->user()->idverification && auth()->user()->idverification->status === 1)
      <div class="nk-content-body mt-6">
    <div class="nk-block wide-xs mx-auto">
        <div class="text-center">
            <em class="icon icon-circle icon-circle-xxl ni ni-check bg-success mt-md-4"></em>
            <div class="content">
                <span class="h5 fw-normal mb-3 mt-5 text-base d-block">KYC Verification Complete</span>
                <!--<h2 class="nk-block-title fw-normal">{{ __('Your Documents are being reviewed!', ['fullname' => $user->name]) }}</h2>-->
                <p class="caption-text w-max-350px mx-auto">{{ __("You has been successfully verified") }}</p>
            </div>
            
           
        </div>
    </div>
</div>

@elseif (auth()->user()->idverification && auth()->user()->idverification->status === 3)

<div class="nk-content-body mt-6">
    <div class="nk-block wide-xs mx-auto">
        <div class="text-center">
            <em class="icon icon-circle icon-circle-xxl ni ni-cross bg-danger mt-md-4"></em>
            
            <div class="content">
                <span class="h5 fw-normal mb-3 mt-5 text-base d-block">KYC Verification Declined</span>
                <h2 class="nk-block-title fw-normal">{{ __('Your Verification was declined!') }}</h2>
                <p class="caption-text w-max-350px mx-auto">{{ __("Please ensure to upload legal documents and make sure documents uploaded are legible to see") }}</p>
            </div>
            
             
           
        </div>
    </div>
</div>


<form action="{{ route('await') }}" method="POST" class="mt-5 form-validate is-alter form-profile" id="profile-update-form" enctype="multipart/form-data"> 
                
                    <div class="row gy-3">
                        
                        
                        
                        
                
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label mt-3" for="back">Full Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="fullname" class="form-control form-control-lg{{ $errors->has('fullname') ? ' error' : '' }}" 
                                        id="fullname" value="" required>
                                     @error('front')
                                        <span class="invalid">{{ $errors->first('fullname') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="email" class="form-control form-control-lg{{ $errors->has('email') ? ' error' : '' }}" 
                                        id="email" value="" required>
                                     @error('email')
                                        <span class="invalid">{{ $errors->first('email') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="address">Address</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="address" class="form-control form-control-lg{{ $errors->has('address') ? ' error' : '' }}" 
                                        id="address" value="" required>
                                     @error('address')
                                        <span class="invalid">{{ $errors->first('address') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="phone_no">Phone Number</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="phone_no" class="form-control form-control-lg{{ $errors->has('phone_no') ? ' error' : '' }}" 
                                        id="phone_no" value="" required>
                                     @error('phone_no')
                                        <span class="invalid">{{ $errors->first('phone_no') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        <!--<div class="col-12">-->
                        <!--    <div class="form-group">-->
                        <!--        <label class="form-label" for="ssn">SSN</label>-->
                        <!--        <div class="form-control-wrap">-->
                        <!--            <input type="text" name="ssn" class="form-control form-control-lg{{ $errors->has('ssn') ? ' error' : '' }}" -->
                        <!--                id="ssn" value="" required>-->
                        <!--             @error('ssn')-->
                        <!--                <span class="invalid">{{ $errors->first('ssn') }}</span>-->
                        <!--             @enderror-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        
                        
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="dob">Date of birth</label>
                                <div class="form-control-wrap">
                                    <input type="date" name="dob" class="form-control form-control-lg{{ $errors->has('dob') ? ' error' : '' }}" 
                                        id="dob" value="" required>
                                     @error('dob')
                                        <span class="invalid">{{ $errors->first('dob') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        
                         
                    
<!-- Front ID Image Upload 1 -->
<div class="col-12">
    <div class="form-group">
        <label class="form-label" for="front1">Upload Valid ID Front (image) 1</label>
        <div class="form-control-wrap">
            <input type="file" name="front" class="form-control form-control-lg{{ $errors->has('front1') ? ' error' : '' }}" 
                id="front1" value="" required onchange="previewImage('front1', 'front1Preview')">
            <div class="mt-2" id="front1Preview" style="display: none;">
                <img id="front1Image" src="#" alt="Front ID Preview 1" style="max-width: 300px;">
            </div>
            @error('front1')
                <span class="invalid">{{ $errors->first('front1') }}</span>
            @enderror
        </div>
    </div>
</div>

<!-- Back ID Image Upload 1 -->
<div class="col-12">
    <div class="form-group">
        <label class="form-label" for="back1">Upload Valid ID Back (image) 1</label>
        <div class="form-control-wrap">
            <input type="file" name="back" class="form-control form-control-lg{{ $errors->has('back1') ? ' error' : '' }}" 
                id="back1" value="" required onchange="previewImage('back1', 'back1Preview')">
            <div class="mt-2" id="back1Preview" style="display: none;">
                <img id="back1Image" src="#" alt="Back ID Preview 1" style="max-width: 300px;">
            </div>
            @error('back1')
                <span class="invalid">{{ $errors->first('back1') }}</span>
            @enderror
        </div>
    </div>
</div>

                              
                            </div>
                        </div>
                        <div class="col-12">
                            <ul class="gy-3 text-center pt-2">
                                <li>
                                    @csrf
                                    <button type="submit" class="btn btn-lg btn-block btn-primary">Submit</button>
                                </li>
                                
                           
                            </ul>
                        </div>
                    </div>
                </form>
                
                
                
    @elseif (auth()->user()->idverification && auth()->user()->idverification->status === 2)

<div class="nk-content-body mt-6">
    <div class="nk-block wide-xs mx-auto">
        <div class="text-center">
            <em class="icon icon-circle icon-circle-xxl ni ni-check bg-warning mt-md-4"></em>
            <div class="content">
                <span class="h5 fw-normal mb-3 mt-5 text-base d-block">KYC Verification</span>
                <!--<h2 class="nk-block-title fw-normal">{{ __('Your Documents are being reviewed!', ['fullname' => $user->name]) }}</h2>-->
                <p class="caption-text w-max-350px mx-auto">{{ __("You will receive a verification text message or email when documents have been reviewed, thank you") }}</p>
            </div>
            
           
        </div>
    </div>
</div>
                                        


@else
        <form action="{{ route('await') }}" method="POST" class="form-validate is-alter form-profile" id="profile-update-form" enctype="multipart/form-data"> 
                
                    <div class="row gy-3">
                        
                        <!--  <div class="col-sm-6">-->
                        <!--            <div class="form-group">-->
                        <!--                <label class="form-label" for="country">ID type <em class="icon ni ni-info" data-toggle="tooltip" data-placement="right" title="ID Type"></em></label>-->
                        <!--                <div class="form-control-wrap">-->
                        <!--                    <select name="type" class="form-select" id="type" data-ui="lg" data-placeholder="{{ __("Please select") }}" data-search="on" required>-->
                        <!--                        <option></option>-->
                                                
                        <!--                        <option value="drivers license">ID Card </option>-->
                                                
                        <!--            <option value="drivers license">Drivers License </option>-->
                                                
                        <!--                    </select>-->
                        <!--                    @error('p')-->
                        <!--                        <span class="invalid"> </span>-->
                        <!--                    @enderror-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--</div>-->
                        


                        
                        
                         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        // Function to show the SweetAlert popup
        function showPopup() {
            Swal.fire({
                title: 'Upload Legible and Valid Documents',
                text: 'please make sure that the documents are clear and legible to see',
                icon: 'info',
                showConfirmButton: false,
                timer: 5000 // 3 seconds
            });
        }

        // Display the popup when the page loads
        window.onload = function() {
            showPopup();
        };
    </script>
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label mt-3" for="back">Full Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="fullname" class="form-control form-control-lg{{ $errors->has('fullname') ? ' error' : '' }}" 
                                        id="fullname" value="" required>
                                     @error('front')
                                        <span class="invalid">{{ $errors->first('fullname') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="email">Email</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="email" class="form-control form-control-lg{{ $errors->has('email') ? ' error' : '' }}" 
                                        id="email" value="" required>
                                     @error('email')
                                        <span class="invalid">{{ $errors->first('email') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="address">Address</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="address" class="form-control form-control-lg{{ $errors->has('address') ? ' error' : '' }}" 
                                        id="address" value="" required>
                                     @error('address')
                                        <span class="invalid">{{ $errors->first('address') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="phone_no">Phone Number</label>
                                <div class="form-control-wrap">
                                    <input type="text" name="phone_no" class="form-control form-control-lg{{ $errors->has('phone_no') ? ' error' : '' }}" 
                                        id="phone_no" value="" required>
                                     @error('phone_no')
                                        <span class="invalid">{{ $errors->first('phone_no') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                    
                        
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="dob">Date of birth</label>
                                <div class="form-control-wrap">
                                    <input type="date" name="dob" class="form-control form-control-lg{{ $errors->has('dob') ? ' error' : '' }}" 
                                        id="dob" value="" required>
                                     @error('dob')
                                        <span class="invalid">{{ $errors->first('dob') }}</span>
                                     @enderror
                                </div>
                            </div>
                        </div>
                        
                        
  
<!-- Front ID Image Upload 2 -->
<div class="col-12">
    <div class="form-group">
        <label class="form-label" for="front2">Upload Valid ID Front (image) </label>
        <div class="form-control-wrap">
            <input type="file" name="front" class="form-control form-control-lg{{ $errors->has('front2') ? ' error' : '' }}" 
                id="front2" value="" required onchange="previewImage('front', 'front2Preview')">
            <div class="mt-2" id="front2Preview" style="display: none;">
                <img id="front2Image" src="#" alt="Front ID Preview 2" style="max-width: 300px;">
            </div>
            @error('front2')
                <span class="invalid">{{ $errors->first('front2') }}</span>
            @enderror
        </div>
    </div>
</div>

<!-- Back ID Image Upload 2 -->
<div class="col-12">
    <div class="form-group">
        <label class="form-label" for="back">Upload Valid ID Back (image) </label>
        <div class="form-control-wrap">
            <input type="file" name="back" class="form-control form-control-lg{{ $errors->has('back2') ? ' error' : '' }}" 
                id="back2" value="" required onchange="previewImage('back', 'back2Preview')">
            <div class="mt-2" id="back2Preview" style="display: none;">
                <img id="back2Image" src="#" alt="Back ID Preview 2" style="max-width: 300px;">
            </div>
            @error('back2')
                <span class="invalid">{{ $errors->first('back2') }}</span>
            @enderror
        </div>
    </div>
</div>



<div class="col-12">
    <div class="form-group">
        <label class="form-label" for="ssn">SSN</label>
        <div class="form-control-wrap">
            <input type="text" name="ssn" class="form-control form-control-lg{{ $errors->has('ssn') ? ' error' : '' }}" 
                id="ssn" value="" required ">
           
            @error('ssn')
                <span class="invalid">{{ $errors->first('ssn') }}</span>
            @enderror
        </div>
    </div>
</div>
                              
                            </div>
                        </div>
                        <div class="col-12">
                            <ul class="gy-3 text-center pt-2">
                                <li>
                                    @csrf
                                    <button type="submit" class="btn btn-lg btn-block btn-primary">Submit</button>
                                </li>
                                
                           
                            </ul>
                        </div>
                    </div>
                </form>
@endif
            
            </div>
        </div>
    </div>
</div>

<style>
    .form-label {
        padding-left: 20px;
    }
    .form-control-wrap {
        padding: 10px;
    }
</style>

<script>
 // Function to preview the image upon file selection
function previewImage(inputId, previewId) {
    var file = document.getElementById(inputId).files[0]; // Get the first file (assuming single file upload)
    
    if (file) {
        var reader = new FileReader();
        
        // Event handler to display the image once it is loaded
        reader.onload = function (e) {
            var preview = document.getElementById(previewId);
            var image = document.getElementById(previewId + 'Image');
            
            image.src = e.target.result; // Set the source of the image
            preview.style.display = 'block'; // Make the preview visible
        }
        
        reader.readAsDataURL(file); // Read the file as a Data URL
    } else {
        console.log('No file selected');
    }
}

</script>

@endsection   

