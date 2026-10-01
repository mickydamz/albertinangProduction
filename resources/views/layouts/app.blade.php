<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global CSS Variables - Essential for Navigation Styling */
        :root {
            --ds-color-alias-body-background-light: #fff;
            --ds-color-alias-border: #d8d8d8;
            --ds-color-alias-text-default: #404040;
            --ds-color-link: #375ea9;
            --ds-color-star: #EFC71D;
            --ds-color-accent: #c00;

            --argos-orange: #4e7a1a;
            --argos-dark-orange: #2e600d;
            --argos-light-grey: #eaf3de;
            --argos-medium-grey: #666;
            --argos-dark-grey: #333;
            --argos-accent-blue: #007bff;

            --ds-font-family-base: "Roboto", Arial, Helvetica, sans-serif;
            --ds-font-size-body-1: 1rem;
            --ds-font-stack-body-1-line-height: 1.5;
            --ds-font-stack-body-1-font-weight: 400;

            --ds-modifier-opacity-60: 0.6;
            --tw-ring-color: rgb(147 197 253/var(--ds-modifier-opacity-60,0.6));
        }

        /* Universal Resets - Essential for consistent rendering */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            border: 0 solid var(--ds-color-alias-border);
            --tw-ring-color: rgb(147 197 253/var(--ds-modifier-opacity-60,0.6));
        }

        body {
            font-family: var(--ds-font-family-base);
            font-size: var(--ds-font-size-body-1);
            line-height: var(--ds-font-stack-body-1-line-height);
            color: var(--ds-color-alias-text-default);
            background-color: var(--ds-color-alias-body-background-light);
            font-weight: var(--ds-font-stack-body-1-font-weight);
        }

        html {
            -webkit-text-size-adjust: 100%;
            font-feature-settings: normal;
            -webkit-tap-highlight-color: transparent;
            font-variation-settings: normal;
            tab-size: 4;
            -moz-tab-size: 4;
            -o-tab-size: 4;
        }

        div, section, ul, li, a, button, img, span, h1, h2, h3, h4, p {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        ul {
            list-style: none;
        }

        a {
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        button {
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            font-feature-settings: inherit;
            font-family: inherit;
            font-size: 100%;
            font-variation-settings: inherit;
            font-weight: inherit;
            letter-spacing: inherit;
            line-height: inherit;
            margin: 0;
            padding: 0;
            text-transform: none;
            -webkit-appearance: button;
            background-color: transparent;
            background-image: none;
        }

        img {
            display: block;
            vertical-align: middle;
            height: auto;
            max-width: 100%;
        }

        svg {
            display: block;
            vertical-align: middle;
        }

        /* Top Banner */
        .top-banner {
            background: linear-gradient(90deg, var(--argos-dark-grey) 0%, #4a5e70 100%);
            color: white;
            padding: 8px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            font-size: 14px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
        }

        .top-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .banner-content {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            gap: 20px;
        }

        .banner-content span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .banner-content i {
            color: var(--argos-orange);
        }

        /* Header */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px 0;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            gap: 20px;
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            color: var(--argos-dark-grey);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .logo span {
            color: var(--argos-orange);
        }

        /* Desktop Categories Dropdown */
        .desktop-categories {
            position: relative;
            margin-right: 15px;
        }

        .desktop-categories-btn {
            background: var(--argos-light-grey);
            color: var(--argos-dark-grey);
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .desktop-categories-btn:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .desktop-categories-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 280px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 0 0 8px 8px;
            z-index: 100;
            display: none;
            padding: 5px 0;
            animation: fadeInDown 0.3s ease-out;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .desktop-categories:hover .desktop-categories-menu {
            display: block;
        }

        .desktop-category-item {
            padding: 12px 20px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--argos-dark-grey);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .desktop-category-item:last-child {
            border-bottom: none;
        }

        .desktop-category-item:hover {
            background: #fff5ed;
            color: var(--argos-orange);
            transform: translateX(5px);
        }

        .desktop-category-item i {
            width: 20px;
            text-align: center;
            color: var(--argos-medium-grey);
        }
        .desktop-category-item:hover i {
            color: var(--argos-orange);
        }

        /* Desktop Subcategories */
        .desktop-category-item.has-subcategories {
            position: relative;
        }

        .desktop-category-item.has-subcategories .sub-arrow {
            margin-left: auto;
            font-size: 10px;
            color: var(--argos-medium-grey);
        }

        .desktop-Subcategory-menu {
            position: absolute;
            top: 0;
            left: 100%;
            width: 220px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            border-radius: 0 8px 8px 0;
            z-index: 110;
            display: none;
            border-left: 1px solid #eaf3de;
            padding: 8px 0;
            animation: fadeInRight 0.3s ease-out;
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .desktop-category-item.has-subcategories:hover .desktop-Subcategory-menu {
            display: block;
        }

        .desktop-Subcategory-item {
            padding: 10px 18px;
            border-bottom: 1px solid #f5f5f5;
            color: var(--argos-dark-grey);
            font-size: 14px;
            display: block;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .desktop-Subcategory-item:last-child {
            border-bottom: none;
        }

        .desktop-Subcategory-item:hover {
            background: #e6f2ff;
            color: var(--argos-accent-blue);
            transform: translateX(5px);
        }

        /* Search Container (Desktop) */
        .search-container {
            flex: 1;
            max-width: 600px;
            margin: 0;
            position: relative;
            min-width: 150px;
        }

        .search-form {
            display: flex;
            border: 2px solid var(--argos-light-grey);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .search-form:focus-within {
            border-color: var(--argos-orange);
            box-shadow: 0 0 0 3px rgba(255, 79, 0, 0.1);
        }

        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            font-size: 14px;
            outline: none;
            min-width: 0;
        }

        .search-btn {
            background: var(--argos-orange);
            color: white;
            border: none;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }

        .search-btn:hover {
            background: var(--argos-dark-orange);
            transform: translateY(-1px);
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-decoration: none;
            color: var(--argos-dark-grey);
            font-size: 14px;
            transition: all 0.3s ease, transform 0.2s ease;
            white-space: nowrap;
        }

        .header-btn:hover {
            border-color: var(--argos-orange);
            color: var(--argos-orange);
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(255, 79, 0, 0.1);
        }

        .cart-count {
            background: var(--argos-orange);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 5px;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none; /* Hidden by default, shown in media query */
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--argos-dark-grey);
            padding: 5px;
            transition: transform 0.2s ease;
        }

        .mobile-menu-btn:hover {
            transform: scale(1.1);
        }

        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
            display: none;
            transition: opacity 0.3s ease;
            opacity: 0;
        }
        .mobile-menu-overlay.active {
            opacity: 1;
            display: block;
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            left: -100%;
            width: 85%;
            max-width: 320px;
            height: 100vh;
            background: white;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            z-index: 1100;
            transition: left 0.3s ease;
            overflow-y: auto;
        }

        .mobile-menu.active {
            left: 0;
        }

        .mobile-menu-header {
            padding: 15px;
            background: var(--argos-orange);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mobile-menu-close {
            background: none;
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .mobile-menu-close:hover {
            transform: rotate(90deg);
        }

        .mobile-menu-content {
            padding: 15px;
        }

        /* Mobile Categories */
        .mobile-category-item {
            padding: 12px 15px;
            border-bottom: 1px solid #eaf3de;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--argos-dark-grey);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-category-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            color: var(--argos-medium-grey);
        }

        .mobile-category-item.has-subcategories::after {
            content: '\f054';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 12px;
            color: var(--argos-medium-grey);
            transition: transform 0.2s ease;
        }

        .mobile-subcategories {
            display: none;
            padding-left: 15px;
            background: #f9f9f9;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease-out;
        }
        .mobile-subcategories.active {
            max-height: 500px;
            display: block;
        }

        .mobile-Subcategory-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            color: var(--argos-dark-grey);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .mobile-Subcategory-item:last-child {
            border-bottom: none;
        }

        .mobile-Subcategory-item:hover {
            background: #fff5ed;
            color: var(--argos-orange);
            transform: translateX(3px);
        }

        /* Active states */
        .mobile-category-item.active {
            color: var(--argos-orange);
            background: #fff5ed;
        }

        .mobile-category-item.active i {
            color: var(--argos-orange);
        }

        .mobile-category-item.active.has-subcategories::after {
            content: '\f078';
            color: var(--argos-orange);
            transform: rotate(90deg);
        }

        /* Mobile Search Toggle Button */
        .mobile-search-toggle-btn {
            display: none; /* Hidden by default, shown in media query */
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--argos-dark-grey);
            padding: 5px;
            transition: transform 0.2s ease;
        }
        .mobile-search-toggle-btn:hover {
            transform: scale(1.1);
        }

        /* Mobile Search Bar (appears under nav) */
        .mobile-search-bar {
            display: none; /* Hidden by default */
            width: 100%;
            padding: 10px 20px; /* Adjust padding as needed */
            background-color: var(--ds-color-alias-body-background-light); /* Match header background */
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); /* Subtle shadow */
            position: absolute; /* Position it below the header */
            top: 100%; /* Place it right below the header */
            left: 0;
            z-index: 999; /* Below header, above page content */
            transition: all 0.3s ease-out;
            opacity: 0;
            transform: translateY(-10px); /* Start slightly above and slide down */
        }

        .header.mobile-search-active .mobile-search-bar {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .mobile-search-bar .search-form {
            display: flex;
            border: 2px solid var(--argos-light-grey);
            border-radius: 8px;
            overflow: hidden;
        }

        .mobile-search-bar .search-input {
            flex: 1;
            padding: 10px 15px;
            border: none;
            font-size: 16px;
            outline: none;
        }

        .mobile-search-bar .search-btn {
            background: var(--argos-orange);
            color: white;
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .mobile-search-bar .search-btn:hover {
            background: var(--argos-dark-orange);
        }

        /* Responsive Design for Header */
        @media (max-width: 1200px) {
            .header-container, .banner-content {
                padding: 0 15px;
            }
        }

        @media (max-width: 992px) {
            .header-actions {
                display: flex;
            }
            .header-actions .header-btn:first-child {
                display: flex;
            }
            .header-actions .header-btn span {
                display: none;
            }
            .header-actions .header-btn i {
                margin-right: 0;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 10px 0;
                position: relative; /* Needed for absolute positioning of mobile-search-bar */
            }
            
            .logo {
                font-size: 28px;
                flex-shrink: 0;
            }
            
            .logo span {
                font-size: 20px;
            }
            
            .mobile-menu-btn {
                display: block; /* Show hamburger on mobile */
            }
            
            .desktop-categories {
                display: none; /* Hide desktop categories on mobile */
            }
            
            .header-container {
                flex-wrap: nowrap;
                justify-content: space-between;
                gap: 10px;
            }

            .header-actions {
                order: 3;
                margin-left: auto;
                gap: 10px;
            }

            .header-actions .header-btn {
                padding: 8px 10px;
                border: none;
                background: none;
                color: var(--argos-dark-grey);
                position: relative;
            }

            .header-actions .header-btn span {
                display: none;
            }

            .header-actions .header-btn i {
                margin-right: 0;
                font-size: 18px;
            }

            .cart-count {
                top: -5px;
                right: -5px;
            }

            /* Hide desktop search container on mobile */
            .search-container {
                display: none;
            }

            /* Show mobile search toggle button */
            .mobile-search-toggle-btn {
                display: block;
                order: 1; /* Place it before other actions if needed */
            }
        }

        @media (max-width: 480px) {
            .banner-content {
                flex-direction: column;
                gap: 5px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <div class="banner-content">
            <span><i class="fas fa-truck"></i> Free Delivery on orders over £50</span>
            <span><i class="fas fa-star"></i> Nectar Points on every purchase</span>
        </div>
    </div>

    <!-- Header (New Navigation) -->
    <header class="header">
        <div class="header-container">
            <button class="mobile-menu-btn" aria-label="Open navigation menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <a href="#" class="logo" aria-label="Argos Home">ARGOS<span>.</span></a>
            
            <!-- Desktop Categories Dropdown -->
            <div class="desktop-categories">
                <button class="desktop-categories-btn" aria-expanded="false" aria-controls="desktop-categories-menu">
                    <i class="fas fa-th-large"></i> Shop Categories
                </button>
                <div class="desktop-categories-menu" id="desktop-categories-menu">
                    <div class="desktop-category-item has-subcategories">
                        <i class="fas fa-laptop"></i> Technology <i class="fas fa-chevron-right sub-arrow"></i>
                        <div class="desktop-Subcategory-menu">
                            <a href="#" class="desktop-Subcategory-item">Laptops & PCs</a>
                            <a href="#" class="desktop-Subcategory-item">TVs & Accessories</a>
                            <a href="#" class="desktop-Subcategory-item">Phones & Smartwatches</a>
                            <a href="#" class="desktop-Subcategory-item">Gaming Consoles</a>
                            <a href="#" class="desktop-Subcategory-item">Cameras & Drones</a>
                        </div>
                    </div>
                    <div class="desktop-category-item has-subcategories">
                        <i class="fas fa-home"></i> Home & Furniture <i class="fas fa-chevron-right sub-arrow"></i>
                        <div class="desktop-Subcategory-menu">
                            <a href="#" class="desktop-Subcategory-item">Living Room Furniture</a>
                            <a href="#" class="desktop-Subcategory-item">Bedroom Furniture</a>
                            <a href="#" class="desktop-Subcategory-item">Kitchen & Dining</a>
                            <a href="#" class="desktop-Subcategory-item">Home Decor</a>
                            <a href="#" class="desktop-Subcategory-item">Lighting</a>
                        </div>
                    </div>
                    <div class="desktop-category-item has-subcategories">
                        <i class="fas fa-tshirt"></i> Clothing & Jewellery <i class="fas fa-chevron-right sub-arrow"></i>
                        <div class="desktop-Subcategory-menu">
                            <a href="#" class="desktop-Subcategory-item">Women's Clothing</a>
                            <a href="#" class="desktop-Subcategory-item">Men's Clothing</a>
                            <a href="#" class="desktop-Subcategory-item">Kids' Clothing</a>
                            <a href="#" class="desktop-Subcategory-item">Watches</a>
                            <a href="#" class="desktop-Subcategory-item">Jewellery</a>
                        </div>
                    </div>
                    <div class="desktop-category-item has-subcategories">
                        <i class="fas fa-baby"></i> Baby & Nursery <i class="fas fa-chevron-right sub-arrow"></i>
                        <div class="desktop-Subcategory-menu">
                            <a href="#" class="desktop-Subcategory-item">Prams & Pushchairs</a>
                            <a href="#" class="desktop-Subcategory-item">Car Seats</a>
                            <a href="#" class="desktop-Subcategory-item">Nursery Furniture</a>
                            <a href="#" class="desktop-Subcategory-item">Baby Toys</a>
                        </div>
                    </div>
                    <div class="desktop-category-item has-subcategories">
                        <i class="fas fa-dumbbell"></i> Sports & Leisure <i class="fas fa-chevron-right sub-arrow"></i>
                        <div class="desktop-Subcategory-menu">
                            <a href="#" class="desktop-Subcategory-item">Fitness Equipment</a>
                            <a href="#" class="desktop-Subcategory-item">Outdoor Play</a>
                            <a href="#" class="desktop-Subcategory-item">Camping & Hiking</a>
                            <a href="#" class="desktop-Subcategory-item">Cycling</a>
                        </div>
                    </div>
                    <div class="desktop-category-item">
                        <i class="fas fa-robot"></i> Toys
                    </div>
                    <div class="desktop-category-item">
                        <i class="fas fa-utensils"></i> Kitchen & Appliances
                    </div>
                    <div class="desktop-category-item">
                        <i class="fas fa-tools"></i> DIY & Garden
                    </div>
                    <div class="desktop-category-item">
                        <i class="fas fa-book"></i> Books & Media
                    </div>
                    <div class="desktop-category-item">
                        <i class="fas fa-paw"></i> Pet Care
                    </div>
                </div>
            </div>
            
            <!-- Desktop Search Container (hidden on mobile) -->
            <div class="search-container">
                <form class="search-form">
                    <input type="text" class="search-input" placeholder="Search for products, brands or categories" aria-label="Search through site content">
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>

            <div class="header-actions">
                <!-- Mobile Search Toggle Button (visible on mobile, triggers mobile search bar) -->
                <button class="mobile-search-toggle-btn" aria-label="Toggle search bar">
                    <i class="fas fa-search"></i>
                </button>
                
                <a href="#" class="header-btn"><i class="fas fa-user"></i> <span>Account</span></a>
                <a href="/cart" class="header-btn" id="basketButton">
                    <i class="fas fa-shopping-cart">
                    
                </i> <span>Basket</span>
                <span class="cart-count">0</span></a>
            </div>
        </div>
        <!-- Mobile Search Bar (appears under nav on mobile) -->
        <div class="mobile-search-bar" id="mobileSearchBar">
            <form class="search-form">
                <input type="text" class="search-input" placeholder="Search for products, brands or categories" aria-label="Mobile search input">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu-overlay"></div>
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <h3>Shop Categories</h3>
            <button class="mobile-menu-close" aria-label="Close navigation menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-menu-content">
            <div class="mobile-category-item has-subcategories">
                <span><i class="fas fa-laptop"></i> Technology</span>
            </div>
            <div class="mobile-subcategories">
                <div class="mobile-Subcategory-item">Laptops & PCs</div>
                <div class="mobile-Subcategory-item">TVs & Accessories</div>
                <div class="mobile-Subcategory-item">Phones & Smartwatches</div>
                <div class="mobile-Subcategory-item">Gaming Consoles</div>
                <div class="mobile-Subcategory-item">Cameras & Drones</div>
            </div>
            
            <div class="mobile-category-item has-subcategories">
                <span><i class="fas fa-home"></i> Home & Furniture</span>
            </div>
            <div class="mobile-subcategories">
                <div class="mobile-Subcategory-item">Living Room Furniture</div>
                <div class="mobile-Subcategory-item">Bedroom Furniture</div>
                <div class="mobile-Subcategory-item">Kitchen & Dining</div>
                <div class="mobile-Subcategory-item">Home Decor</div>
                <div class="mobile-Subcategory-item">Lighting</div>
            </div>
            
            <div class="mobile-category-item has-subcategories">
                <span><i class="fas fa-tshirt"></i> Clothing & Jewellery</span>
            </div>
            <div class="mobile-subcategories">
                <div class="mobile-Subcategory-item">Women's Clothing</div>
                <div class="mobile-Subcategory-item">Men's Clothing</div>
                <div class="mobile-Subcategory-item">Kids' Clothing</div>
                <div class="mobile-Subcategory-item">Watches</div>
                <div class="mobile-Subcategory-item">Jewellery</div>
            </div>
            
            <div class="mobile-category-item has-subcategories">
                <span><i class="fas fa-baby"></i> Baby & Nursery</span>
            </div>
            <div class="mobile-subcategories">
                <div class="mobile-Subcategory-item">Prams & Pushchairs</div>
                <div class="mobile-Subcategory-item">Car Seats</div>
                <div class="mobile-Subcategory-item">Nursery Furniture</div>
                <div class="mobile-Subcategory-item">Baby Toys</div>
            </div>
            
            <div class="mobile-category-item has-subcategories">
                <span><i class="fas fa-dumbbell"></i> Sports & Leisure</span>
            </div>
            <div class="mobile-subcategories">
                <div class="mobile-Subcategory-item">Fitness Equipment</div>
                <div class="mobile-Subcategory-item">Outdoor Play</div>
                <div class="mobile-Subcategory-item">Camping & Hiking</div>
                <div class="mobile-Subcategory-item">Cycling</div>
            </div>
            
            <div class="mobile-category-item">
                <span><i class="fas fa-robot"></i> Toys</span>
            </div>
            <div class="mobile-category-item">
                <span><i class="fas fa-utensils"></i> Kitchen & Appliances</span>
            </div>
            <div class="mobile-category-item">
                <span><i class="fas fa-tools"></i> DIY & Garden</span>
            </div>
            <div class="mobile-category-item">
                <span><i class="fas fa-book"></i> Books & Media</span>
            </div>
            <div class="mobile-category-item">
                <span><i class="fas fa-paw"></i> Pet Care</span>
            </div>
        </div>
    </div>

    <!-- Fullscreen Search Overlay (Removed as per request) -->
    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mobile Menu Toggle (Hamburger)
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
            const mobileMenu = document.querySelector('.mobile-menu');
            const mobileMenuClose = document.querySelector('.mobile-menu-close');

            if (mobileMenuBtn && mobileMenuOverlay && mobileMenu && mobileMenuClose) {
                mobileMenuBtn.addEventListener('click', () => {
                    mobileMenu.classList.add('active');
                    mobileMenuOverlay.classList.add('active');
                });

                mobileMenuClose.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    mobileMenuOverlay.classList.remove('active');
                });

                mobileMenuOverlay.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    mobileMenuOverlay.classList.remove('active');
                });
            }

            // Mobile Categories Dropdown
            const mobileCategoryItems = document.querySelectorAll('.mobile-category-item.has-subcategories');
            mobileCategoryItems.forEach(item => {
                item.addEventListener('click', () => {
                    item.classList.toggle('active');
                    const subcategories = item.nextElementSibling;
                    if (subcategories && subcategories.classList.contains('mobile-subcategories')) {
                        subcategories.classList.toggle('active');
                    }
                });
            });

            // Mobile Search Bar Toggle
            const mobileSearchToggleBtn = document.querySelector('.mobile-search-toggle-btn');
            const headerElement = document.querySelector('.header'); // The header element to toggle the class on
            const mobileSearchBarInput = document.querySelector('#mobileSearchBar .search-input');

            if (mobileSearchToggleBtn && headerElement && mobileSearchBarInput) {
                mobileSearchToggleBtn.addEventListener('click', () => {
                    // Toggle the 'mobile-search-active' class on the header
                    const isActive = headerElement.classList.toggle('mobile-search-active');
                    if (isActive) {
                        mobileSearchBarInput.focus(); // Focus the input when the search bar appears
                    }
                });
            }
        });
    </script>
</body>
</html>
