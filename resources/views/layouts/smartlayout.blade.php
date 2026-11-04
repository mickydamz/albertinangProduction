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
    <title>Albertina</title>
    <link rel="apple-touch-icon" href="{{ asset('/app-asset/images/logo/logo.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/app-asset/images/logo/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
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
    
    <style>
     .horizontal-layout {
    background: linear-gradient(to bottom, rgba(255, 255, 255, 0.90), rgba(255, 255, 255, 0.90)),
    url("{{ asset('app-assets/images/backgrounds/bg.svg') }}");
  /* background-color: #cccccc; Used if the image is unavailable */
  height: 500px; /* You must set a specified height */
  background-position: center; /* Center the image */
  background-repeat: no-repeat; /* Do not repeat the image */
  background-size: cover;
  font-weight: bold;
  font-size: 1.1rem; 
   }

.smart-bg{
    
     background: linear-gradient(to bottom, rgba(255, 255, 255, 0.90), rgba(255, 255, 255, 0.90)),
    url("{{ asset('app-assets/images/backgrounds/bg.svg') }}");
  /* background-color: #cccccc; Used if the image is unavailable */
  height: 500px; /* You must set a specified height */
  background-position: center; /* Center the image */
  background-repeat: no-repeat; /* Do not repeat the image */
  background-size: cover;
  font-weight: bold;
  font-size: 1.1rem; 
    
}
   .bg{
  opacity: 90%;
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  height: auto;

   }

   
    </style>
@livewireStyles
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('/asset/css/style.css') }}">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="horizontal-layout horizontal-menu  navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="">

 <script type="text/javascript"> 
        function googleTranslateElementInit() { 
            new google.translate.TranslateElement(
                {pageLanguage: 'en'}, 
                'google_translate_element'
            ); 
        } 
    </script> 
      
    <script type="text/javascript" src=
"https://translate.google.com/translate_a/element.js?
        cb=googleTranslateElementInit">
    </script> 
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<style>#google_translate_element,.skiptranslate{display:none;}body{top:0!important;}</style>
<div id="google_translate_element"></div>
<script>

const userLocale =
  navigator.languages && navigator.languages.length
    ? navigator.languages[0]
    : navigator.language;

console.log(userLocale); // 👉️ "en-US"



// 👇️ ["en-US", "en", "de"]
console.log(navigator.languages);






if (userLocale.includes('en')) {
  var included = 'en'

} else if (userLocale.includes('fr')) {
  var included = 'fr'
} else if (userLocale.includes('es')) {
  var included = 'es'
} else if (userLocale.includes('es_CO') || userLocale == 'es_CO') {
  var included = 'es'
}else if (userLocale.includes('es_MX') || userLocale == 'es_MX') {
  var included = 'es'
}else if (userLocale.includes('es_MX') || userLocale == 'es_MX') {
  var included = 'es'
}else if (userLocale.includes('es_SV') || userLocale == 'es_SV') {
  var included = 'es'
}else if (userLocale.includes('it')) {
  var included = 'it'
} else if (userLocale.includes('pt')) {
  var included = 'pt'
} else if (userLocale.includes('de')) {
  var included = 'de'
}else if (userLocale.includes('ar')) {
  var included = 'ar'
}else if (userLocale.includes('id')) {
  var included = 'id'
}else if (userLocale.includes('sl')) {
  var included = 'sl'
} else {
 var included = 'en'
}


$.ajax({ 
    url: "http://ajaxhttpheaders.appspot.com", 
    dataType: 'jsonp', 
    success: function(headers) {
        language = headers['Accept-Language'];
        alert(headers['Accept-Language']);
        var language = window.navigator.userLanguage || window.navigator.language;
        if (language.includes('en')) {
  var included = 'en'

} else if (language.includes('fr')) {
  var included = 'fr'
} else if (language.includes('es')) {
  var included = 'es'
} else if (language.includes('es-MX')) {
  var included = 'es'
}else if (language.includes('es-SV')) {
  var included = 'es'
}else if (language.includes('es-CO')) {
  var included = 'es'
}else if (language.includes('es-VE')) {
  var included = 'es'
}else if (language.includes('it')) {
  var included = 'it'
} else if (language.includes('pt')) {
  var included = 'pt'
} else if (language.includes('de')) {
  var included = 'de'
}else if (language.includes('ar')) {
  var included = 'ar'
}else if (language.includes('id')) {
  var included = 'id'
}else if (language.includes('sl')) {
  var included = 'sl'
} else {
 var included = 'en'
}
        function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en', 
            includedLanguages: included, 
            autoDisplay: false
        }, 'google_translate_element');
        var a = document.querySelector("#google_translate_element select");
        a.selectedIndex=1;
        a.dispatchEvent(new Event('change'));
    }
    
   
    }
});


    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en', 
            includedLanguages: included, 
            autoDisplay: false
        }, 'google_translate_element');
        var a = document.querySelector("#google_translate_element select");
        a.selectedIndex=1;
        a.dispatchEvent(new Event('change'));
    }
    
   
    

</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

   
   <style>
       a.dropdown-toggle.dropdown-indicator.has-indicator.nav-link{
           visibility:hidden;
       }
   </style>
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar-expand-lg navbar navbar-fixed align-items-center navbar-shadow navbar-brand-center" data-nav="brand-center">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav">
                <li class="nav-item"><a class="navbar-brand" href="/"><span class="brand-logo"><img src="{{ asset('/app-asset/images/logo/logo.png') }}"/>
                            </span>
                        <h2 class="brand-text mb-0">Albertina</h2>
                    </a></li>
            </ul>
        </div>
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="#"><i class="ficon" data-feather="menu"></i></a></li>
                </ul>
                
                
            </div>
            <ul class="nav navbar-nav align-items-center ms-auto">
                
                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a></li>
                
<!--                 
                <li class="nav-item dropdown dropdown-notification me-25"><a class="nav-link" href="#" data-bs-toggle="dropdown"><i class="ficon" data-feather="bell"></i><span class="badge rounded-pill bg-danger badge-up">5</span></a>
                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-end">
                        <li class="dropdown-menu-header">
                            <div class="dropdown-header d-flex">
                                <h4 class="notification-title mb-0 me-auto">Notifications</h4>
                                <div class="badge rounded-pill badge-light-primary">6 New</div>
                            </div>
                        </li>
                        <li class="scrollable-container media-list"><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar"><img src="{{ asset('/app-asset/images/portrait/small/avatar-s-15.jpg') }}" alt="avatar" width="32" height="32"></div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">Congratulation Sam ðŸŽ‰</span>winner!</p><small class="notification-text"> Won the monthly best seller badge.</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar"><img src="{{ asset('/app-asset/images/portrait/small/avatar-s-3.jpg') }}" alt="avatar" width="32" height="32"></div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">New message</span>&nbsp;received</p><small class="notification-text"> You have 10 unread messages</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar bg-light-danger">
                                            <div class="avatar-content">MD</div>
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">Revised Order ðŸ‘‹</span>&nbsp;checkout</p><small class="notification-text"> MD Inc. order updated</small>
                                    </div>
                                </div>
                            </a>
                            <div class="list-item d-flex align-items-center">
                                <h6 class="fw-bolder me-auto mb-0">System Notifications</h6>
                                <div class="form-check form-check-primary form-switch">
                                    <input class="form-check-input" id="systemNotification" type="checkbox" checked="">
                                    <label class="form-check-label" for="systemNotification"></label>
                                </div>
                            </div><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar bg-light-danger">
                                            <div class="avatar-content"><i class="avatar-icon" data-feather="x"></i></div>
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">Server down</span>&nbsp;registered</p><small class="notification-text"> USA Server is down due to hight CPU usage</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar bg-light-success">
                                            <div class="avatar-content"><i class="avatar-icon" data-feather="check"></i></div>
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">Sales report</span>&nbsp;generated</p><small class="notification-text"> Last month sales report generated</small>
                                    </div>
                                </div>
                            </a><a class="d-flex" href="#">
                                <div class="list-item d-flex align-items-start">
                                    <div class="me-1">
                                        <div class="avatar bg-light-warning">
                                            <div class="avatar-content"><i class="avatar-icon" data-feather="alert-triangle"></i></div>
                                        </div>
                                    </div>
                                    <div class="list-item-body flex-grow-1">
                                        <p class="media-heading"><span class="fw-bolder">High memory</span>&nbsp;usage</p><small class="notification-text"> BLR Server using high memory</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="dropdown-menu-footer"><a class="btn btn-primary w-100" href="#">Read all notifications</a></li>
                    </ul>
                </li> -->
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none">
                            <span class="user-name fw-bolder">{{ auth()->user()->email }}</span><span class="user-status"></span></div><span class="avatar">
                                <!-- <img class="round" src="{{ asset('/app-asset/images/portrait/small/avatar-s-11.jpg') }}" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span> -->

                                @if(isset(auth()->user()->fileName))
                                                                  <img class="round" src="{{ asset('storage/'.auth()->user()->fileName) }}" alt="avatar" height="40" width="40" />
                                                                   @else
                                                                   <img class="round" src="{{ asset('/app-asset/images/portrait/small/e_avatar.png') }}" alt="avatar" height="40" width="40">
                                                     @endif 
                                                                   <span class="avatar-status-online"></span>
                                                                   
                
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdown-user"><a class="dropdown-item" href="/page-account-settings-account"><i class="me-50" data-feather="user"></i> Profile</a>
                   @if(auth()->user()->email == 'kennethbolly@gmail.com')
                    <div class="dropdown-divider"></div><a class="dropdown-item" href="/adminhome"><i class="me-50" data-feather="settings"></i> Admin</a>
                    @endif
                   
                    <div class="dropdown-divider"></div><a class="dropdown-item" href="page-account-settings-security"><i class="me-50" data-feather="settings"></i> Settings</a>
                    <div class="dropdown-divider"></div><a class="dropdown-item" href="/logout"><i class="me-50" data-feather="power"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <ul class="main-search-list-defaultlist d-none">
        <li class="d-flex align-items-center"><a href="#">
                <h6 class="section-label mt-75 mb-0">Files</h6>
            </a></li>
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
        <li class="d-flex align-items-center"><a href="#">
                <h6 class="section-label mt-75 mb-0">Members</h6>
            </a></li>
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
                    <li class="nav-item me-auto"><a class="navbar-brand" href="/"><span class="brand-logo"><img src="{{ asset('/app-asset/images/logo/logo.png') }}"/>
                                </span>
                            <h2 class="brand-text mb-0">Albertina</h2>
                        </a></li>
                    <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i></a></li>
                </ul>
            </div>
            <div class="shadow-bottom"></div>
            <!-- Horizontal menu content-->
            <div class="navbar-container main-menu-content" data-menu="menu-container">
                <!-- include /includes/mixins-->
                <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
                
                    <li class="nav-item"><a class="nav-link d-flex align-items-center" href="/main" ><i data-feather="home"></i><span data-i18n="Dashboard">Dashboard</span></a>
                       
                    </li>


                    <li class="nav-item"><a class="nav-link d-flex align-items-center" href="/portfolio-plans" ><i data-feather="home"></i><span data-i18n="Dashboards">Transactions</span></a>
                       
                    </li>


                    <li class="nav-item"><a class="nav-link d-flex align-items-center" href="/planing" ><i data-feather="home"></i><span data-i18n="Dashboards">Investment</span></a>
                       
                       </li>


                       <!--<li class="nav-item"><a class="nav-link d-flex align-items-center" href="/plans" ><i data-feather="home"></i><span data-i18n="Dashboards">Our Plans</span></a>-->
                       
                       <!--</li>-->

                       <li class="nav-item"><a class="nav-link d-flex align-items-center" href="/page-account-settings-account" ><i data-feather="home"></i><span data-i18n="Dashboards">Profile</span></a>
                       
                       </li>

                       


                       



                   <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown"><i data-feather="box"></i><span data-i18n="Misc">Withdraw</span></a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            
                            
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="/withdraw-create" data-bs-toggle="" data-i18n="Documentation"><i data-feather="folder"></i><span data-i18n="Documentation">Make withdrawal</span></a>
                            </li>
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="/withdraw-history" data-bs-toggle="" data-i18n="Raise Support" ><i data-feather="life-buoy"></i><span data-i18n="Raise Support">Withdrawal History</span></a>
                            </li>
                        </ul>
                   </li>
                    

                    
                    <!-- <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown"><i data-feather="box"></i><span data-i18n="Misc">Trades</span></a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            
                            
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="https://Albertina.com/demo/vuexy-html-bootstrap-admin-template/documentation" data-bs-toggle="" data-i18n="Documentation" ><i data-feather="folder"></i><span data-i18n="Documentation">Documentation</span></a>
                            </li>
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="https://Albertina.ticksy.com/" data-bs-toggle="" data-i18n="Raise Support" ><i data-feather="life-buoy"></i><span data-i18n="Raise Support">Raise Support</span></a>
                            </li>
                        </ul>
                   </li> -->



                   <!-- <li class="dropdown nav-item" data-menu="dropdown"><a class="dropdown-toggle nav-link d-flex align-items-center" href="#" data-bs-toggle="dropdown"><i data-feather="box"></i><span data-i18n="Misc">Trades</span></a>
                        <ul class="dropdown-menu" data-bs-popper="none">
                            
                            
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="https://Albertina.com/demo/vuexy-html-bootstrap-admin-template/documentation" data-bs-toggle="" data-i18n="Documentation" ><i data-feather="folder"></i><span data-i18n="Documentation">Documentation</span></a>
                            </li>
                            <li data-menu=""><a class="dropdown-item d-flex align-items-center" href="https://Albertina.ticksy.com/" data-bs-toggle="" data-i18n="Raise Support" ><i data-feather="life-buoy"></i><span data-i18n="Raise Support">Raise Support</span></a>
                            </li>
                        </ul>
                   </li> -->



                </ul>
            </div>
        </div>
    </div>
    <!-- END: Main Menu-->

    @yield('content')

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-start d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2021<a class="ms-25" href="https://1.envato.market/Albertina_portfolio" >Albertina</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span><span class="float-md-end d-none d-md-block"></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('/app-asset/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('/app-asset/vendors/js/ui/jquery.sticky.js') }}"></script>
    <script src="{{ asset('/app-asset/vendors/js/charts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('/app-asset/vendors/js/extensions/toastr.min.js') }}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ asset('/app-asset/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('/app-asset/js/core/app.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ asset('/app-asset/js/scripts/pages/dashboard-ecommerce.js') }}"></script>
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
    
    <!-- <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="8b8bdc18-a6a6-432e-9eec-54290642a584";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script> -->

@livewireScripts
</body>
<!-- END: Body-->

</html>