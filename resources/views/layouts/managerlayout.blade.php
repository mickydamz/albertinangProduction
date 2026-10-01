<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Albertina &amp;Defi.">
    <meta name="keywords" content="Albertina Defi">
    <meta name="author" content="Albertina">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Albertina - Manager</title>
    {{-- Favicon -- place inside <head> in your layouts/adminlayout.blade.php --}}
<link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-chrome-192x192.png') }}">
<link rel="icon" type="image/png" sizes="512x512" href="{{ asset('favicon/android-chrome-512x512.png') }}">
<link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/app-asset/images/logo/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/vendors/css/charts/apexcharts.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/vendors/css/extensions/toastr.min.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/themes/bordered-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/themes/semi-dark-layout.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/core/menu/menu-types/horizontal-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/pages/dashboard-ecommerce.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/plugins/charts/chart-apex.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/app-asset/css/plugins/extensions/ext-component-toastr.css') }}">
    <!-- END: Page CSS-->
    
    @livewireStyles
    
    <style>
        .horizontal-layout {
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.90), rgba(255, 255, 255, 0.90)),
            url("{{ asset('app-assets/images/backgrounds/bg.svg') }}");
            height: 500px;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            font-weight: bold;
            font-size: 1.1rem; 
        }

        .bg {
            opacity: 90%;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: auto;
        }

        section, table {
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.90), rgba(255, 255, 255, 0.90)),
            url("{{ asset('app-assets/images/backgrounds/bg.svg') }}");
            height: 7rem;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            font-weight: bold;
            font-size: 1.1rem; 
        }

        .small-text {
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-menu {
            max-width: 250px;
        }

        .dropdown-item {
            padding: 8px 12px;
        }
    </style>

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/asset/css/style.css') }}">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu navbar-floating footer-static" data-open="hover" data-menu="horizontal-menu" data-col="">

    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center" data-nav="brand-center">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a class="navbar-brand" href="/manager/dashboard">
                        <span class="brand-logo"></span>
                        <h2 class="brand-text mb-0">Albertina</h2>
                    </a>
                </li>
            </ul>
        </div>
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item">
                        <a class="nav-link menu-toggle" href="#">
                            <i class="ficon" data-feather="menu"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link nav-link-style">
                        <i class="ficon" data-feather="moon"></i>
                    </a>
                </li>
                
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none">
                            <span class="user-name fw-bolder">{{ auth()->user()->email ?? 'Manager' }}</span>
                            <span class="user-status">Manager</span>
                        </div>
                        <span class="avatar">
                            @if(isset(auth()->user()->avatar))
                                <img class="round" src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="avatar" height="40" width="40" />
                            @else
                                <img class="round" src="{{ asset('/app-asset/images/portrait/small/avatar-s-11.jpg') }}" alt="avatar" height="40" width="40">
                            @endif 
                            <span class="avatar-status-online"></span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/manager/dashboard">
                            <i class="me-50" data-feather="home"></i> Dashboard
                        </a>
                        <a class="dropdown-item" href="/manager/products">
                            <i class="me-50" data-feather="box"></i> Products
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout">
                            <i class="me-50" data-feather="power"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    
    <ul class="main-search-list-defaultlist d-none">
        <li class="d-flex align-items-center"><a href="#"><h6 class="section-label mt-75 mb-0">Files</h6></a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager">
            <div class="d-flex">
                <div class="me-75"><img src="{{ asset('/app-asset/images/icons/xls.png') }}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                </div>
            </div><small class="search-data-size me-50 text-muted">&apos;17kb</small>
        </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager">
            <div class="d-flex">
                <div class="me-75"><img src="{{ asset('/app-asset/images/icons/jpg.png') }}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div><small class="search-data-size me-50 text-muted">&apos;11kb</small>
        </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager">
            <div class="d-flex">
                <div class="me-75"><img src="{{ asset('/app-asset/images/icons/pdf.png') }}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div><small class="search-data-size me-50 text-muted">&apos;150kb</small>
        </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between w-100" href="app-file-manager">
            <div class="d-flex">
                <div class="me-75"><img src="{{ asset('/app-asset/images/icons/doc.png') }}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                </div>
            </div><small class="search-data-size me-50 text-muted">&apos;256kb</small>
        </a></li>
        <li class="d-flex align-items-center"><a href="#"><h6 class="section-label mt-75 mb-0">Members</h6></a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img src="{{ asset('/app-asset/images/portrait/small/avatar-s-8.jpg') }}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">John Doe</p><small class="text-muted">UI designer</small>
                </div>
            </div>
        </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img src="{{ asset('/app-asset/images/portrait/small/avatar-s-1.jpg') }}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
        </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img src="{{ asset('/app-asset/images/portrait/small/avatar-s-14.jpg') }}" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
        </a></li>
        <li class="auto-suggestion"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="app-user-view-account">
            <div class="d-flex align-items-center">
                <div class="avatar me-75"><img src="{{ asset('/app-asset/images/portrait/small/avatar-s-6.jpg') }}" alt="png" height="32"></div>
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
                    <li class="nav-item me-auto">
                        <a class="navbar-brand" href="/manager/dashboard">
                            <span class="brand-logo"></span>
                            <h2 class="brand-text mb-0">Albertina</h2>
                        </a>
                    </li>
                    <li class="nav-item nav-toggle">
                        <a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse">
                            <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="shadow-bottom"></div>
            <!-- Horizontal menu content-->
            <div class="navbar-container main-menu-content" data-menu="menu-container">
                <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
                    
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center" href="/manager/dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="small-text">Dashboard</span>
                        </a>
                    </li>

                    <!-- Products Menu -->
                    <li class="dropdown nav-item" data-menu="dropdown">
                        <a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-box"></i>
                            <span class="small-text">Products</span>
                        </a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/manager/products">
                                    <i class="fas fa-list me-1"></i>
                                    <span>All Products</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/manager/products?filter=assigned">
                                    <i class="fas fa-tag me-1"></i>
                                    <span>My Assigned Products</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                   

                </ul>
            </div>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0">
            <span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; {{ date('Y') }} Albertina. All rights reserved.</span>
            <span class="float-md-end d-none d-md-block">Hand-crafted & Made with <i data-feather="heart"></i></span>
        </p>
    </footer>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('/app-asset/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('/app-asset/vendors/js/ui/jquery.sticky.js') }}"></script>
    <script src="{{ asset('/app-asset/vendors/js/charts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('/app-asset/vendors/js/extensions/toastr.min.js') }}"></script>
    <!-- END: Page Vendor JS-->
     
    <script src="{{ asset('/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/app-assets/vendors/js/tables/datatable/responsive.bootstrap5.js') }}"></script>
    
    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('/app-asset/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('/app-asset/js/core/app.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ asset('/app-asset/js/scripts/pages/dashboard-ecommerce.js') }}"></script>
    <!-- END: Page JS-->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        });
    </script>

    @livewireScripts

    @stack('scripts')
    @include('partials.notify')

</body>
</html>