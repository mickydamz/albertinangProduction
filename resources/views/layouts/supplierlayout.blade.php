<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Prime Supply Liquor</title>
    <link rel="apple-touch-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

<!-- BEGIN: Vendor CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/charts/apexcharts.css') }}">
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
<!-- END: Theme CSS-->

<!-- BEGIN: Page CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/horizontal-menu.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/dashboard-ecommerce.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/charts/chart-apex.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/extensions/ext-component-toastr.css') }}">
<!-- END: Page CSS-->
@livewireStyles
@livewireScripts
<!-- BEGIN: Custom CSS-->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
<!-- END: Custom CSS-->
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/673ec31e4304e3196ae607c0/1id6ikbee';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="">

@if(auth()->user()->verified == 1)


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
               
                <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name fw-bolder">{{ auth()->user()->email }}</span><span class="user-status">{{ auth()->user()->name }}</span></div><span class="avatar">
                        @if(auth()->user()->avatar)
                        <img class="round" src="{{ Storage::url(auth()->user()->avatar) }}" alt="avatar" height="40" width="40">


@else
 
<img class="round" src="\app-asset\images\portrait\small\e_avatar.png" alt="avatar" height="40" width="40">
@endif   
                        
                        <span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="page-account-settings-account"><i class="me-50" data-feather="user"></i> Profile</a>
                         <a class="dropdown-item" href="/chat"><i class="me-50" data-feather="message-square"></i> Chats</a>
                        <!-- <a class="dropdown-item" href="app-email.html"><i class="me-50" data-feather="mail"></i> Inbox</a>
                        <a class="dropdown-item" href="app-todo.html"><i class="me-50" data-feather="check-square"></i> Task</a>
                        <a class="dropdown-item" href="app-chat.html"><i class="me-50" data-feather="message-square"></i> Chats</a>
                        <div class="dropdown-divider"></div><a class="dropdown-item" href="page-account-settings-account.html"><i class="me-50" data-feather="settings"></i> Settings</a>
                        <a class="dropdown-item" href="page-pricing.html"><i class="me-50" data-feather="credit-card"></i> Pricing</a>
                        <a class="dropdown-item" href="page-faq.html"><i class="me-50" data-feather="help-circle"></i> FAQ</a> -->
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

    <li class="dropdown nav-item" data-menu="dropdown">
        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <i data-feather="dollar-sign"></i> <!-- Unique icon for supplier -->
            <span data-i18n="Deposit">Deposit</span>
        </a>
        <ul class="dropdown-menu" data-bs-popper="none">
            <li data-menu="">
                <a class="dropdown-item d-flex align-items-center" href="/deposit-create" data-i18n="Make Deposit">
                    <i data-feather="arrow-up-circle"></i> <!-- Unique icon for deposit -->
                    <span data-i18n="Make Deposit">Make Deposit</span>
                </a>
            </li>
            <li data-menu="">
                <a class="dropdown-item d-flex align-items-center" href="/my-deposits" data-i18n="Deposit History">
                    <i data-feather="clock"></i> <!-- Unique icon for deposit history -->
                    <span data-i18n="Deposit History">Deposit History</span>
                </a>
            </li>
        </ul>
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
    <a class="nav-link d-flex align-items-center" href="/kyc-verification">
        <i data-feather="shield"></i> 
        <span data-i18n="id">KYC</span>
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

<!-- Non-affiliate users can access profile and deposit history -->
@if(auth()->user()->hasRole('user'))
    <li class="nav-item">
        <a class="nav-link d-flex align-items-center" href="/page-account-settings-account">
            <i data-feather="user"></i> <!-- Icon for profile -->
            <span data-i18n="Profile">Profile</span>
        </a>
    </li>

    <li class="dropdown nav-item" data-menu="dropdown">
        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <i data-feather="box"></i> <!-- Icon for deposit -->
            <span data-i18n="Deposit">Deposit</span>
        </a>
        <ul class="dropdown-menu" data-bs-popper="none">
            <li data-menu="">
                <a class="dropdown-item d-flex align-items-center" href="/deposit-create" data-i18n="Make Deposit">
                    <i data-feather="arrow-up-circle"></i>
                    <span data-i18n="Make Deposit">Make Deposit</span>
                </a>
            </li>
            <li data-menu="">
                <a class="dropdown-item d-flex align-items-center" href="/my-deposits" data-i18n="Deposit History">
                    <i data-feather="clock"></i>
                    <span data-i18n="Deposit History">Deposit History</span>
                </a>
            </li>
        </ul>
    </li>
@endif

                   
                 
            
                </ul>
            </div>
        </div>
    </div>
 
       @yield('content')



  
       @else
<!-- Sweet alert Positions -->
<section id="sweet-alert-position" class="d-flex justify-content-center align-items-center vh-100">
    <div class="row">
        <div class="col-sm-12">
            <div class="card border-light shadow-sm">
                <div class="card-header bg-light">
                    <h4 class="card-title text-dark">Dear {{ auth()->user()->name}}, verification notice</h4>
                </div>
                <div class="card-body">
                    <p class="card-text mb-0 text-muted">
                        You need to be verified to view this page. Please complete the verification process to continue.
                    </p>
                    <a href="/logout" class="btn btn-outline-primary mt-3">Logout</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/ Sweet alert Positions -->




 
@endif
     
    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2021
            <a class="ms-25" href="https://primesupplyliquor.com" target="_blank"></a>
        <span class="d-none d-sm-inline-block">, All rights Reserved</span></span><span class="float-md-end d-none d-md-block">Prime Supply Liquor</span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->

    <script src="https://cdn.jsdelivr.net/npm/livewire-v2.12.8/dist/livewire.js"></script>

 <!-- BEGIN: Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- END: Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/ui/jquery.sticky.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('app-assets/js/core/app.js') }}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-ecommerce.js') }}"></script>
<!-- END: Page JS-->

    <!-- END: Page JS-->
 

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>

@include('partials.notify')
</body>
<!-- END: Body-->

</html>