<html class="loaded" lang="en" data-textdirection="ltr" style="--vh: 5.99px;"><!-- BEGIN: Head--><head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
        <meta name="description" content="Albertina.">
    <meta name="keywords" content="Albertina">
    <meta name="author" content="Albertina">
    <title>Cask Supply Liquor</title>
    <link rel="apple-touch-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/wizard/bs-stepper.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/spinner/jquery.bootstrap-touchspin.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/extensions/toastr.min.css') }}">
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/bordered-layout.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}">

<!-- BEGIN: Page CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/horizontal-menu.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-ecommerce.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/form-wizard.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/ext-component-toastr.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/form-number-input.css') }}">
<!-- END: Page CSS-->

<!-- BEGIN: Custom CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
<!-- END: Custom CSS-->

        <!-- END: Custom CSS-->
    </head>
  
    <script>
        // This ensures that the page is hidden until everything is loaded
window.addEventListener('load', function() {
    // Hide the loader
    document.getElementById('page-loader').style.display = 'none';
    
    // Show the page content
    document.getElementById('page-content').style.display = 'block';
});


    </script>
    <style>
        /* Custom style to ensure spinner is visible in the center */
        #page-content {
            display: none;
        }
    </style>

<!-- BEGIN: Loader -->
<div id="page-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.9) url('https://i.gifer.com/YCZH.gif') center center no-repeat; z-index: 9999;">
    <!-- Optional: You can use a GIF or custom spinner as the loader -->
</div>

    <body  id="page-content"  class="pace-done horizontal-layout navbar-floating footer-static menu-hide vertical-layout vertical-overlay-menu fixed-navbar" data-open="hover" data-menu="horizontal-menu" data-col=""><div class="pace pace-inactive"><div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">

      <!-- Pace.js Loader (Spinner) -->
      <div class="pace pace-active">
        <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
    </div>
  

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>

<script>
    // Use setTimedo {
         Use setTimeout to simulate a 3-second delay
    } while (condition);
    window.addEventListener('load', function() {
        setTimeout(function() {
            // Hide Pace.js spinner
            document.querySelector('.pace').style.display = 'none';
            
            // Show page content after 3 seconds
            document.getElementById('page-content').style.display = 'block';
        }, 3000); // 3000ms = 3 seconds
    });
</script>

  <div class="pace-progress-inner"></div>
</div>
<div class="pace-activity"></div></div>
        <!-- BEGIN: Header-->
          <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-left" data-nav="brand-left">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav">
            <li class="nav-item">
    <a class="navbar-brand" href="https://Albertina.com" style="display: flex; align-items: center; text-decoration: none;">
        <span class="brand-logo" style="margin-right: 10px; margin-left: 30px;">
            <img width="50" height="50" src="{{ asset('/app-asset/images/logo/logo.png') }}" alt="Logo"/>
        </span>
        <span style="font-size: 1.5rem; font-weight: bold; color: #9E6A3A; margin: 0;">Albertina</span>
    </a>
</li>

            </ul>
        </div>
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
                
              
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
            <li class="nav-item position-relative">
    @php
        $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
    @endphp
    <a class="nav-link d-flex align-items-center" href="{{ route('cart.index') }}">
        <!-- Feather Icon with slightly reduced size -->
        <i data-feather="shopping-cart" class="feather-2x" style="width: 30px; height: 30px;"></i> <!-- Smaller width/height -->
        @if($cartCount > 0)
            <span class="badge bg-danger rounded-pill position-absolute" style="top: 0; right: 0; font-size: 0.8rem; padding: 0.2em 0.4em; width: 18px; height: 18px; text-align: center; line-height: 1.1;">
                {{ $cartCount }}
            </span> <!-- Positioned inside the icon -->
        @endif
    </a>
</li>

<!-- Add some margin to create space between the cart and the user profile dropdown -->
<li class="nav-item dropdown dropdown-user" style="margin-left: 15px;"> <!-- Adjust margin as needed -->
    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="avatar">
            @if(auth()->user()->avatar)
                <img class="round" src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar" height="40" width="40">
            @else
                <img class="round" src="\app-asset\images\portrait\small\e_avatar.png" alt="avatar" height="40" width="40">
            @endif
            <span class="avatar-status-online"></span> <!-- Optional online status -->
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
        <!-- User details displayed on separate lines -->
        <div class="dropdown-item user-info">
            <span class="user-name fw-bolder d-block">
                {{ \Illuminate\Support\Str::limit(auth()->user()->name, 20, '...') }} <!-- Truncate name -->
            </span>
            <span class="user-status text-muted d-block">
                {{ \Illuminate\Support\Str::limit(auth()->user()->email, 30, '...') }} <!-- Truncate email -->
            </span>
        </div>
        @if(!auth()->user()->hasRole('affiliate'))
            <a class="dropdown-item" href="page-account-settings-account"><i class="me-50" data-feather="user"></i> Profile</a>
            <a class="dropdown-item" href="/chat"><i class="me-50" data-feather="message-square"></i> Chats</a>
        @endif
        <a class="dropdown-item" href="/logout"><i class="me-50" data-feather="power"></i> Logout</a>
    </div>
</li>



            </ul>
        </div>
    </nav>
    <ul class="main-search-list-defaultlist d-none">
        <li class="d-flex align-items-center"><a href="#">
                <h6 class="section-label mt-75 mb-0">Files</h6>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
                <div class="d-flex">
                    <div class="me-75"><img src="../../../app-assets/images/icons/xls.png" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                    </div>
                </div><small class="search-data-size me-50 text-muted">&apos;17kb</small>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
                <div class="d-flex">
                    <div class="me-75"><img src="../../../app-assets/images/icons/jpg.png" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                    </div>
                </div><small class="search-data-size me-50 text-muted">&apos;11kb</small>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
                <div class="d-flex">
                    <div class="me-75"><img src="../../../app-assets/images/icons/pdf.png" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                    </div>
                </div><small class="search-data-size me-50 text-muted">&apos;150kb</small>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager.html">
                <div class="d-flex">
                    <div class="me-75"><img src="../../../app-assets/images/icons/doc.png" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                    </div>
                </div><small class="search-data-size me-50 text-muted">&apos;256kb</small>
            </a></li>
        <li class="d-flex align-items-center"><a href="#">
                <h6 class="section-label mt-75 mb-0">Members</h6>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account.html">
                <div class="d-flex align-items-center">
                    <div class="avatar me-75"><img src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account.html">
                <div class="d-flex align-items-center">
                    <div class="avatar me-75"><img src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account.html">
                <div class="d-flex align-items-center">
                    <div class="avatar me-75"><img src="../../../app-assets/images/portrait/small/avatar-s-14.jpg" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                    </div>
                </div>
            </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account.html">
                <div class="d-flex align-items-center">
                    <div class="avatar me-75"><img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="png" height="32"></div>
                    <div class="search-data">
                        <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                    </div>
                </div>
            </a></li>
    </ul>
    <ul class="main-search-list-defaultlist-other-list d-none">
        <li class="auto-suggestion justify-content-between"><a class="d-flex align-items-center justify-content-between w-100 py-50">
                <div class="d-flex justify-content-start"><span class="me-75" data-feather="alert-circle"></span><span>No results found.</span></div>
            </a></li>
    </ul>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <div class="horizontal-menu-wrapper">
        <div class="header-navbar navbar-expand-sm navbar navbar-horizontal floating-nav navbar-light navbar-shadow menu-border container-xxl" role="navigation" data-menu="menu-wrapper" data-menu-type="floating-nav">
            <div class="navbar-header">
                <ul class="nav navbar-nav flex-row">
                    <li class="nav-item me-auto"><a class="navbar-brand" href="https://Albertina.com"><span class="brand-logo">
                    <img src="{{ asset('/app-asset/images/logo/logo.png') }}" alt="avatar" height="40" width="40">
                            </span>
                            <h2 class="brand-text mb-0">Cask Liquor</h2>
                        </a></li>
                    <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i></a></li>
                </ul>
            </div>
            <div class="shadow-bottom"></div>
            <!-- Horizontal menu content-->
            <div class="navbar-container main-menu-content" data-menu="menu-container">
                <!-- include ../../../includes/mixins-->
                <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">


                @if(auth()->user()->hasRole('affiliate'))
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/affiliate/dashboard">
            <i data-feather="home"></i>
            <span data-i18n="Dashboard">Dashboard</span>
        </a>
    </li>

    <li class="dropdown nav-item" data-menu="dropdown">
        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <i data-feather="dollar-sign"></i> <!-- Unique icon for affiliate -->
            <span data-i18n="Withdrawal">Withdrawal</span>
        </a>
        <ul class="dropdown-menu" data-bs-popper="none">
            <li data-menu="">
                <a class="dropdown-item d-flex align-items-center" href="/withdraw-create" data-i18n="Make Withdrawal">
                    <i data-feather="arrow-down-circle"></i> <!-- Unique icon for withdrawal -->
                    <span data-i18n="Make Withdrawal">Make Withdrawal</span>
                </a>
            </li>
            <li data-menu="">
                <a class="dropdown-item d-flex align-items-center" href="/withdrawals" data-i18n="Withdrawal History">
                    <i data-feather="history"></i>
                    <span data-i18n="Withdrawal History">Withdrawal History</span>
                </a>
            </li>
        </ul>
    </li>
@endif

@if(auth()->user()->hasRole('user'))
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/dashboard">
            <i data-feather="home"></i>
            <span data-i18n="Dashboard">Dashboard</span>
        </a>
    </li>

    @php
        $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
    @endphp

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/cart">
            <i data-feather="shopping-cart"></i>
            <span data-i18n="Cart">Cart</span>
            @if($cartCount > 0)
                <span class="badge bg-danger ms-2">{{ $cartCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/transactions">
            <i data-feather="file-text"></i> <!-- Unique icon for transactions -->
            <span data-i18n="Transactions">Transactions</span>
        </a>
    </li>

    <li class="nav-item">
    <a class="nav-link d-flex align-items-center" href="/products">
        <i data-feather="shopping-bag"></i> <!-- Feather shopping bag icon -->
        <span data-i18n="Manage Orders">All Products</span>
    </a>
</li>


    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/supplier/distributors">
            <i data-feather="clipboard"></i>
            <span data-i18n="Manage Orders">All Distributors</span>
        </a>
    </li> 
@endif

@if(auth()->user()->hasRole('supplier'))
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/supplier/dashboard">
            <i data-feather="box"></i>
            <span data-i18n="Supplier Dashboard">Dashboard</span>
        </a>
    </li>

    

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/supplier/products">
            <i data-feather="list"></i> <!-- Unique icon for products -->
            <span data-i18n="Manage Products">Manage Products</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/supplier/transactions">
            <i data-feather="activity"></i> <!-- Unique icon for supplier transactions -->
            <span data-i18n="Transactions">Transactions</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/withdraw-create">
            <i data-feather="arrow-up-circle"></i> <!-- Unique icon for withdrawal -->
            <span data-i18n="Make Withdrawal">Make Withdrawal</span>
        </a>
    </li>
@endif

<!-- Non-affiliate users can access profile and deposit history -->
@if(!auth()->user()->hasRole('affiliate'))
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/page-account-settings-account">
            <i data-feather="user"></i> <!-- Icon for profile -->
            <span data-i18n="Profile">Profile</span>
        </a>
    </li>

    
@endif

                  
                   
                 
                    <!-- <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown"><i data-feather="box"></i><span data-i18n="Misc">Misc</span></a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-bs-toggle="dropdown" data-i18n="Menu Levels"><i data-feather="menu"></i><span data-i18n="Menu Levels">Menu Levels</span></a>
                                <ul class="dropdown-menu" data-bs-popper="none">
                                    <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="" data-i18n="Second Level"><i data-feather="circle"></i><span data-i18n="Second Level">Second Level 2.1</span></a>
                                    </li>
                                    <li class="dropdown dropdown-submenu" data-menu="dropdown-submenu"><a class="dropdown-item d-flex align-items-center dropdown-toggle" href="#" data-bs-toggle="dropdown" data-i18n="Second Level"><i data-feather="circle"></i><span data-i18n="Second Level">Second Level 2.2</span></a>
                                        <ul class="dropdown-menu" data-bs-popper="none">
                                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="" data-i18n="Third Level"><i data-feather="circle"></i><span data-i18n="Third Level">Third Level 3.1</span></a>
                                            </li>
                                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="" data-i18n="Third Level"><i data-feather="circle"></i><span data-i18n="Third Level">Third Level 3.2</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="disabled" data-menu=""><a class="dropdown-item d-flex align-items-center" href="" data-bs-toggle="" data-i18n="Disabled Menu"><i data-feather="eye-off"></i><span data-i18n="Disabled Menu">Disabled Menu</span></a>
                            </li>
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="https://pixinvent.com/demo/vuexy-html-bootstrap-admin-template/documentation" data-bs-toggle="" data-i18n="Documentation" target="_blank"><i data-feather="folder"></i><span data-i18n="Documentation">Documentation</span></a>
                            </li>
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="https://pixinvent.ticksy.com/" data-bs-toggle="" data-i18n="Raise Support" target="_blank"><i data-feather="life-buoy"></i><span data-i18n="Raise Support">Raise Support</span></a>
                            </li>
                        </ul>
                    </li> -->


                </ul>
            </div>
        </div>
    </div>
 

        <!-- BEGIN: Content-->
        <div class="app-content content ecommerce-application">
            <div class="content-overlay"></div>
            <div class="header-navbar-shadow"></div>
            <div class="content-wrapper container-xxl p-0">
                <div class="content-header row">
                    
                  
                </div>
                <div class="content-body">
                    @yield('content')
                    
                </div>
            </div>
        </div>
        <!-- END: Content-->

        <div class="sidenav-overlay" style="touch-action: pan-y; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div>
        <div class="drag-target" style="touch-action: pan-y; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div>

        <!-- BEGIN: Footer-->
        <footer class="footer footer-static footer-light">
            <p class="clearfix mb-0">
                <span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT © 2021<a class="ms-25" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Pixinvent</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span><span class="float-md-end d-none d-md-block">Hand-crafted &amp; Made with<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></span>
            </p>
        </footer>
        <button class="btn btn-primary btn-icon scroll-top waves-effect waves-float waves-light" type="button" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
        </button>
        <!-- END: Footer-->

  <!-- BEGIN: Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- END: Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/ui/jquery.sticky.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/wizard/bs-stepper.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/spinner/jquery.bootstrap-touchspin.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('app-assets/js/scripts/pages/app-ecommerce-checkout.js') }}"></script>
<!-- END: Page JS-->

     
    
    

</body>

</html>