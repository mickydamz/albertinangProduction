@extends('layouts.simslayout')
@section('content')

<style>
    /* Custom Font-Face from provided CSS */
    @font-face {
        font-display: auto;
        font-family: Tu;
        font-style: normal;
        font-weight: 400;
        src: url(https://cdn.tu.co.uk/fonts/Tu_W_Rg.woff2);
    }

    @font-face {
        font-display: auto;
        font-family: Tu;
        font-style: normal;
        font-weight: 600;
        src: url(https://cdn.tu.co.uk/fonts/Tu_W_Bd.woff2);
    }

    @font-face {
        font-display: auto;
        font-family: Tu;
        font-style: normal;
        font-weight: 300;
        src: url(https://cdn.tu.co.uk/fonts/Tu_W_Lt.woff2);
    }

    /* Global CSS Variables */
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
        --ds-font-size-body-1: 0.95rem;
        --ds-font-stack-body-1-line-height: 1.4;
        --ds-font-stack-body-1-font-weight: 400;

        --ds-modifier-opacity-60: 0.6;
        --tw-ring-color: rgb(147 197 253/var(--ds-modifier-opacity-60,0.6));
    }

    /* Universal Resets */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        border: 0;
        --tw-ring-color: rgb(147 197 253/var(--ds-modifier-opacity-60,0.6));
    }

    html {
        -webkit-text-size-adjust: 100%;
        font-feature-settings: normal;
        -webkit-tap-highlight-color: transparent;
        font-variation-settings: normal;
        tab-size: 4;
        scroll-behavior: smooth;
    }

    body {
        font: var(--ds-font-stack-body-1-font-weight) var(--ds-font-size-body-1)/var(--ds-font-stack-body-1-line-height) var(--ds-font-family-base);
        color: var(--ds-color-alias-text-default);
        background-color: var(--ds-color-alias-body-background-light);
    }

    /* Combined common element resets */
    div, section, ul, li, a, button, img, span, h1, h2, h3, h4, p {
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
        text-transform: none;
        -webkit-appearance: button;
        background: none;
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

    /* Top Utility Navigation */
    .top-utility-nav {
        background-color: #f8f8f8;
        padding: 6px 0;
        font-size: 12px;
        color: var(--argos-medium-grey);
        border-bottom: 1px solid #eee;
    }

    .utility-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .utility-container a {
        color: var(--argos-medium-grey);
        margin-left: 12px;
        transition: color 0.2s ease;
    }

    .utility-container a:hover {
        color: var(--argos-dark-grey);
        text-decoration: underline;
    }

    /* Top Banner */
    .top-banner {
        background: linear-gradient(90deg, var(--argos-dark-grey) 0%, #4a5e70 100%);
        color: white;
        padding: 6px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
        font-size: 13px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
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
        padding: 0 15px;
        gap: 15px;
    }

    .banner-content span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .banner-content i {
        color: var(--argos-orange);
    }

    /* Header */
    .header {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        position: sticky;
        top: 0;
        z-index: 1000;
        padding: 10px 0;
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
        gap: 15px;
    }

    .logo {
        font-size: 28px;
        font-weight: 800;
        color: var(--argos-dark-grey);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .logo span {
        color: var(--argos-orange);
    }

    /* Desktop Categories Dropdown */
    .desktop-categories {
        position: relative;
        margin-right: 12px;
    }

    .desktop-categories-btn {
        background: var(--argos-light-grey);
        color: var(--argos-dark-grey);
        padding: 10px 16px;
        border-radius: 5px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.3s ease, transform 0.2s ease;
        font-size: 0.9rem;
    }

    .desktop-categories-btn:hover {
        background: #e0e0e0;
        transform: translateY(-1px);
    }

    .desktop-categories-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 260px;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 0 0 6px 6px;
        z-index: 100;
        display: none;
        padding: 4px 0;
        animation: fadeInDown 0.2s ease-out;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .desktop-categories:hover .desktop-categories-menu {
        display: block;
    }

    .desktop-category-item {
        padding: 10px 16px;
        border-bottom: 1px solid #f7f7f7;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--argos-dark-grey);
        transition: all 0.2s ease;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .desktop-category-item:last-child {
        border-bottom: none;
    }

    .desktop-category-item:hover {
        background: #fffafa;
        color: var(--argos-orange);
        transform: translateX(3px);
    }

    .desktop-category-item i {
        width: 18px;
        text-align: center;
        color: var(--argos-medium-grey);
    }

    .desktop-category-item:hover i {
        color: var(--argos-orange);
    }

    .desktop-category-item.has-subcategories {
        position: relative;
    }

    .desktop-category-item.has-subcategories .sub-arrow {
        margin-left: auto;
        font-size: 9px;
        color: var(--argos-medium-grey);
    }

    .desktop-Subcategory-menu {
        position: absolute;
        top: 0;
        left: 100%;
        width: 200px;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        border-radius: 0 6px 6px 0;
        z-index: 110;
        border-left: 1px solid #f5f5f5;
        padding: 6px 0;
        animation: fadeInRight 0.2s ease-out;
    }

    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(-8px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .desktop-category-item.has-subcategories:hover .desktop-Subcategory-menu {
        display: block;
    }

    .desktop-Subcategory-item {
        padding: 8px 15px;
        border-bottom: 1px solid #f7f7f7;
        color: var(--argos-dark-grey);
        font-size: 0.85rem;
        display: block;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .desktop-Subcategory-item:last-child {
        border-bottom: none;
    }

    .desktop-Subcategory-item:hover {
        background: #eef8ff;
        color: var(--argos-accent-blue);
        transform: translateX(3px);
    }

    /* Search Container (Desktop) */
    .search-container {
        flex: 1;
        max-width: 500px;
        position: relative;
        min-width: 120px;
    }

    .search-form {
        display: flex;
        border: 1px solid var(--argos-light-grey);
        border-radius: 6px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .search-form:focus-within {
        border-color: var(--argos-orange);
        box-shadow: 0 0 0 2px rgba(255, 79, 0, 0.1);
    }

    .search-input {
        flex: 1;
        padding: 10px 12px;
        border: none;
        font-size: 0.9rem;
        outline: none;
        min-width: 0;
    }

    .search-btn {
        background: var(--argos-orange);
        color: white;
        padding: 10px 16px;
        font-weight: 600;
        white-space: nowrap;
        transition: background 0.2s ease, transform 0.1s ease;
        font-size: 0.9rem;
    }

    .search-btn:hover {
        background: var(--argos-dark-orange);
        transform: translateY(-1px);
    }

    /* Header Actions */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        color: var(--argos-dark-grey);
        font-size: 0.85rem;
        position: relative;
        white-space: nowrap;
        transition: all 0.2s ease, transform 0.1s ease;
    }

    .header-btn:hover {
        border-color: var(--argos-orange);
        color: var(--argos-orange);
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(255, 79, 0, 0.1);
    }

    /* Cart Count Badge */
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
        position: absolute;
        top: -6px;
        right: -6px;
        z-index: 10;
        padding: 1px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    /* Mobile Menu */
    .mobile-menu-btn {
        display: none;
        font-size: 18px;
        color: var(--argos-dark-grey);
        padding: 4px;
        transition: transform 0.2s ease;
    }

    .mobile-menu-btn:hover {
        transform: scale(1.05);
    }

    .mobile-menu-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.4);
        z-index: 1050;
        display: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .mobile-menu-overlay.active {
        opacity: 1;
        display: block;
    }

    .mobile-menu {
        position: fixed;
        top: 0;
        left: -100%;
        width: 90%;
        max-width: 300px;
        height: 100vh;
        background: white;
        box-shadow: 1px 0 10px rgba(0,0,0,0.1);
        z-index: 1100;
        overflow-y: auto;
        transition: left 0.2s ease;
    }

    .mobile-menu.active {
        left: 0;
    }

    .mobile-menu-header {
        padding: 12px 15px;
        background: var(--argos-orange);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .mobile-menu-close {
        font-size: 20px;
        color: white;
        transition: transform 0.2s ease;
    }

    .mobile-menu-close:hover {
        transform: rotate(45deg);
    }

    .mobile-menu-content {
        padding: 12px;
    }

    /* Mobile Categories */
    .mobile-category-item {
        padding: 10px 12px;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--argos-dark-grey);
        font-weight: 500;
        transition: all 0.15s ease;
        font-size: 0.9rem;
    }

    .mobile-category-item i {
        width: 18px;
        text-align: center;
        color: var(--argos-medium-grey);
        margin-right: 8px;
    }

    .mobile-category-item.has-subcategories::after {
        content: '\f054';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        font-size: 10px;
        color: var(--argos-medium-grey);
        transition: transform 0.15s ease;
    }

    .mobile-subcategories {
        display: none;
        padding-left: 12px;
        background: #fafafa;
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.2s ease-out;
    }

    .mobile-subcategories.active {
        max-height: 400px;
        display: block;
    }

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
        display: none;
        font-size: 18px;
        color: var(--argos-dark-grey);
        padding: 4px;
        transition: transform 0.2s ease;
    }

    .mobile-search-toggle-btn:hover {
        transform: scale(1.05);
    }

    /* Mobile Search Bar */
    .mobile-search-bar {
        display: none;
        width: 100%;
        padding: 8px 15px;
        background-color: var(--ds-color-alias-body-background-light);
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 999;
        opacity: 0;
        transform: translateY(-8px);
        transition: all 0.2s ease-out;
    }

    .header.mobile-search-active .mobile-search-bar {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .mobile-search-bar .search-form {
        display: flex;
        border: 1px solid var(--argos-light-grey);
        border-radius: 6px;
        overflow: hidden;
    }

    .mobile-search-bar .search-input {
        flex: 1;
        padding: 8px 10px;
        border: none;
        font-size: 0.9rem;
        outline: none;
    }

    .mobile-search-bar .search-btn {
        background: var(--argos-orange);
        color: white;
        padding: 8px 15px;
        font-weight: 600;
        transition: background 0.2s ease;
        font-size: 0.9rem;
    }

    .mobile-search-bar .search-btn:hover {
        background: var(--argos-dark-orange);
    }

    /* New Info Dropdown Styles */
    .info-dropdown {
        position: relative;
        display: inline-block;
    }

    .info-dropdown-btn {
        font-size: 18px;
        color: var(--argos-dark-grey);
        padding: 4px;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .info-dropdown-btn:hover {
        transform: scale(1.05);
        color: var(--argos-orange);
    }

    .info-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        border-radius: 6px;
        z-index: 1000;
        display: none;
        flex-direction: column;
        padding: 8px 0;
        min-width: 160px;
        transform-origin: top right;
        animation: fadeInScale 0.15s ease-out;
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .info-dropdown-menu.active {
        display: flex;
    }

    .info-dropdown-menu a {
        padding: 8px 12px;
        color: var(--argos-dark-grey);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s ease, color 0.15s ease;
        font-size: 0.85rem;
    }

    .info-dropdown-menu a:hover {
        background: var(--argos-light-grey);
        color: var(--argos-orange);
    }

    /* Mini Cart Dropdown */
    .mini-cart-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        width: 280px;
        background: white;
        border-radius: 6px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        z-index: 1000;
        display: none;
        opacity: 0;
        transform: translateY(8px);
        padding: 12px;
        border: 1px solid #eaf3de;
        max-height: 350px;
        overflow-y: auto;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    #basketButton:hover .mini-cart-dropdown,
    .mini-cart-dropdown:hover {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .mini-cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px solid #f5f5f5;
    }

    .mini-cart-header h4 {
        font-size: 15px;
        font-weight: 600;
        color: var(--argos-dark-grey);
    }

    .mini-cart-header .view-cart-link {
        font-size: 12px;
        color: var(--argos-accent-blue);
        display: flex;
        align-items: center;
        gap: 4px;
        transition: color 0.15s ease;
    }

    .mini-cart-header .view-cart-link:hover {
        color: var(--argos-orange);
    }

    .mini-cart-items {
        margin-bottom: 12px;
    }

    .mini-cart-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px dashed #f8f8f8;
    }

    .mini-cart-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .mini-cart-item img {
        width: 45px;
        height: 45px;
        object-fit: contain;
        border-radius: 3px;
        border: 1px solid #f5f5f5;
    }

    .item-details {
        flex-grow: 1;
    }

    .item-details .item-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--argos-dark-grey);
        line-height: 1.2;
    }

    .item-details .item-price {
        font-size: 12px;
        font-weight: 600;
        color: var(--argos-orange);
    }

    .mini-cart-footer {
        padding-top: 12px;
        border-top: 1px solid #f5f5f5;
        text-align: right;
    }

    .mini-cart-footer p {
        font-size: 15px;
        font-weight: 700;
        color: var(--argos-dark-grey);
        margin-bottom: 8px;
    }

    .mini-cart-footer span {
        color: var(--argos-orange);
    }

    .checkout-btn {
        background: var(--argos-orange);
        color: white;
        padding: 9px 15px;
        border-radius: 5px;
        font-weight: 600;
        width: 100%;
        box-shadow: 0 3px 8px rgba(255, 79, 0, 0.15);
        transition: background 0.2s ease;
        font-size: 0.9rem;
    }

    .checkout-btn:hover {
        background: var(--argos-dark-orange);
    }

    .empty-cart-message {
        text-align: center;
        color: var(--argos-medium-grey);
        font-style: italic;
        padding: 15px 0;
        font-size: 0.85rem;
    }

    /* Loader Styles */
    #loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease-out;
        opacity: 1;
        visibility: visible;
    }

    #loader-overlay.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .spinner {
        border: 8px solid #f3f3f3;
        border-top: 8px solid var(--argos-orange);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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
            position: relative;
        }

        .logo {
            font-size: 28px;
            flex-shrink: 0;
        }

        .logo span {
            font-size: 20px;
        }

        .mobile-menu-btn {
            display: block;
        }

        .desktop-categories {
            display: none;
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

        .search-container {
            display: none;
        }

        .mobile-search-toggle-btn {
            display: block;
            order: 1;
        }

        .mini-cart-dropdown {
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            width: 90%;
            max-width: 300px;
        }
    }

    @media (max-width: 480px) {
        .banner-content {
            flex-direction: column;
            gap: 5px;
            font-size: 12px;
        }
    }

    /* Main Content */
    .main-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Section Header */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--argos-light-grey);
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--argos-dark-grey);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        color: var(--argos-orange);
    }

    .section-link {
        font-size: 13px;
        font-weight: 600;
        color: var(--argos-orange);
        display: flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
    }

    .section-link:hover {
        text-decoration: underline;
        color: var(--argos-dark-orange);
        transform: translateX(2px);
    }

    /* General Product Grid for sections */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        overflow-x: hidden;
    }

    .product-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(0,0,0,0.07);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 280px;
        position: relative;
        transition: all 0.2s ease;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .product-image {
        height: 140px;
        background: #fcfcfc;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 8px;
    }

    .product-image img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        transition: transform 0.2s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.03);
    }

    .discount-tag {
        position: absolute;
        top: 8px;
        right: 8px;
        background: var(--argos-orange);
        color: white;
        padding: 3px 6px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .product-info {
        padding: 10px;
        color: var(--argos-dark-grey);
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-title {
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .product-price {
        font-size: 15px;
        font-weight: 700;
        color: var(--argos-orange);
        margin-bottom: 3px;
    }

    .product-original-price {
        font-size: 10px;
        color: var(--argos-medium-grey);
        text-decoration: line-through;
    }

    .items-left {
        font-size: 9px;
        font-weight: 600;
        color: var(--argos-orange);
        margin-top: 3px;
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 1px;
        margin-top: 3px;
    }

    .rating i {
        color: #ffc107;
        font-size: 10px;
    }

    .rating-count {
        font-size: 8px;
        color: var(--argos-medium-grey);
    }

    /* Add to Cart Button Overlay */
    .add-to-cart-btn-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        background: var(--argos-orange);
        color: white;
        padding: 8px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        z-index: 5;
        transform: translateY(100%);
        transition: transform 0.2s ease-out;
    }

    .product-card:hover .add-to-cart-btn-overlay {
        transform: translateY(0);
    }

    /* Contact Info Section */
    .contact-info-section {
        background-color: #f5f5f5;
        padding: 12px 0;
        text-align: center;
        border-bottom: 1px solid #e5e5e5;
        margin-bottom: 20px;
    }

    .contact-info-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .contact-info-container span {
        font-size: 14px;
        font-weight: 500;
        color: var(--argos-dark-grey);
    }

    /* Footer */
    .footer {
        background: var(--argos-dark-grey);
        color: white;
        padding: 30px 0 15px;
        margin-top: 30px;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
    }

    .footer-section h3 {
        margin-bottom: 12px;
        color: var(--argos-orange);
        font-size: 16px;
    }

    .footer-section ul {
        list-style: none;
    }

    .footer-section ul li {
        margin-bottom: 6px;
    }

    .footer-section ul li a {
        color: #c9d2d7;
        text-decoration: none;
        font-size: 13px;
        transition: color 0.2s ease;
    }

    .footer-section ul li a:hover {
        color: var(--argos-orange);
    }

    .payment-methods {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .social-links {
        display: flex;
        gap: 12px;
        margin-top: 15px;
    }

    .social-link {
        background: rgba(255,255,255,0.08);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .social-link:hover {
        background: var(--argos-orange);
        transform: translateY(-2px) scale(1.05);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #3a4b5c;
        margin-top: 20px;
        color: #c9d2d7;
        font-size: 13px;
    }

    .download-app {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }

    .download-btn {
        background: #4a5e70;
        color: white;
        padding: 6px 10px;
        border-radius: 5px;
        font-size: 11px;
        display: flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .download-btn:hover {
        background: var(--argos-orange);
        transform: translateY(-1px);
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeInUp 0.5s ease-out;
    }

    /* Skip Links */
    .skip-link {
        position: absolute;
        top: -9999px;
        left: -9999px;
        width: 1px;
        height: 1px;
        overflow: hidden;
        z-index: 9999;
    }

    .skip-link:focus {
        position: static;
        width: auto;
        height: auto;
        padding: 8px;
        margin: 8px;
        background-color: var(--argos-orange);
        color: white;
        text-decoration: none;
        border-radius: 4px;
        clip: auto;
    }

    /* About Us Page Specific Styles */
    .about-us-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 25px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        text-align: center;
    }

    .about-us-container h1 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--argos-dark-grey);
        margin-bottom: 20px;
        position: relative;
        display: inline-block;
    }

    .about-us-container h1::after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -10px;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: var(--argos-orange);
        border-radius: 2px;
    }

    .about-us-container p {
        font-size: 1.1rem;
        line-height: 1.7;
        color: var(--argos-medium-grey);
        margin-bottom: 15px;
    }

    .about-us-container p:last-of-type {
        margin-bottom: 0;
    }

    .about-us-container strong {
        color: var(--argos-orange);
        font-weight: 700;
    }

    /* Responsive adjustments for About Us page */
    @media (max-width: 768px) {
        .about-us-container {
            margin: 20px 15px;
            padding: 20px;
        }

        .about-us-container h1 {
            font-size: 2rem;
        }

        .about-us-container p {
            font-size: 1rem;
        }
    }

    @media (max-width: 480px) {
        .about-us-container h1 {
            font-size: 1.8rem;
        }

        .about-us-container p {
            font-size: 0.95rem;
        }
    }

    /* Breadcrumb Navigation Styles */
    .breadcrumb {
        max-width: 1200px;
        margin: 15px auto 20px auto;
        padding: 10px 0;
        font-size: 0.9rem;
        color: var(--argos-medium-grey);
        border-bottom: 1px solid var(--border);
    }

    .breadcrumb ul {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .breadcrumb li {
        display: flex;
        align-items: center;
    }

    .breadcrumb li:not(:last-child)::after {
        content: '>';
        margin: 0 8px;
        color: #bbb;
        font-weight: normal;
    }

    .breadcrumb a {
        color: var(--argos-medium-grey);
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .breadcrumb a:hover {
        color: var(--argos-orange);
        text-decoration: underline;
    }

    .breadcrumb li:last-child span {
        color: var(--argos-dark-grey);
        font-weight: 600;
    }
</style>

<!-- Mobile Filter Overlay (from main layout, kept for consistency) -->
<div class="mobile-filter-overlay" id="mobileFilterOverlay"></div>

<!-- Breadcrumb Navigation -->
<nav class="breadcrumb" aria-label="breadcrumb">
    <ul>
        <li><a href="{{ url('/') }}">Home</a></li>
        <li><span>About Us</span></li>
    </ul>
</nav>

<!-- Main Content for About Us Page -->
<main class="main-content" id="main-content">
    <div class="about-us-container animate-in">
        <h1>About Our Store</h1>
        <p>Welcome to <strong>Our Store</strong>, your one-stop shop for high-quality products at unbeatable prices. We are dedicated to providing you with the best shopping experience, combining a vast selection with exceptional customer service.</p>

        <p>Founded in [Year of Establishment], our mission has always been to [Your Mission Statement, e.g., "make quality products accessible to everyone, everywhere"]. We believe in [Your Core Value, e.g., "transparency, customer satisfaction, and continuous improvement"].</p>

        <p>Our team is passionate about [What you are passionate about, e.g., "sourcing the latest trends, ensuring product durability, and building lasting relationships with our customers"]. We work tirelessly to bring you a curated collection of items, from [Product Category 1] to [Product Category 2], and everything in between.</p>

        <p>Thank you for choosing Our Store. We are committed to serving you and look forward to helping you find exactly what you need.</p>
    </div>
</main>

<script>
    // Utility function to debounce events (copied for consistency)
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize global currency variable.
    // This value will be determined by URL parameter, then localStorage, then default 'NGN'.
    window.currentCurrency;
    console.log("SCRIPT START: Initializing window.currentCurrency...");

    // Global currency exchange rates - declared on window to prevent redeclaration errors
    window.currencyExchangeRates = {
        'NGN': { symbol: '₦', rate: 1 },
        'USD': { symbol: '$', rate: 0.00067 }, // Example: 1 NGN = 0.00067 USD
        'GBP': { symbol: '£', rate: 0.00053 }, // Example: 1 NGN = 0.00053 GBP
        'EUR': { symbol: '€', rate: 0.00062 }  // Example: 1 NGN = 0.00062 EUR
    };

    // Function to format price based on current currency (copied for consistency)
    function formatPrice(priceInNaira, currencyCode = window.currentCurrency) {
        console.log(`[formatPrice] Price: ${priceInNaira}, Param Currency: ${currencyCode}, Global Current Currency: ${window.currentCurrency}`);

        const currencyInfo = window.currencyExchangeRates[currencyCode];
        if (!currencyInfo) {
            console.error(`[formatPrice] Unknown currency code: ${currencyCode}. Falling back to NGN.`);
            return `₦${priceInNaira.toLocaleString()}`;
        }
        const convertedPrice = priceInNaira * currencyInfo.rate;
        const formatted = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currencyCode,
            minimumFractionDigits: 0,
            maximumFractionDigits: (currencyCode === 'NGN' || currencyCode === 'USD' || currencyCode === 'GBP' || currencyCode === 'EUR') ? 2 : 0
        }).format(convertedPrice);
        console.log(`[formatPrice] Converted Price: ${convertedPrice}, Formatted Price: ${formatted}`);
        return formatted;
    }

    // Function to update all displayed prices on the page (copied for consistency)
    function updateAllPrices() {
        console.log("updateAllPrices called. Current window.currentCurrency:", window.currentCurrency);
        // This page doesn't have product prices, but the function is kept for consistency if it were to be expanded.
        // If there were price elements, they would be updated here.
        // Example: document.querySelectorAll('.product-card .product-price').forEach(...)
        
        // Update mini-cart prices if applicable
        updateCartDisplay();
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Currency initialization logic (copied for consistency)
        let currencyFromURLRaw = new URLSearchParams(window.location.search).get('currency');
        console.log("DEBUG: About Us Page - Raw Currency from URL:", currencyFromURLRaw);

        const currencyFromURL = currencyFromURLRaw ? currencyFromURLRaw.trim().toUpperCase() : null;
        console.log("DEBUG: About Us Page - Cleaned Currency from URL:", currencyFromURL);

        const storedCurrencyRaw = localStorage.getItem('selectedCurrency');
        console.log("DEBUG: About Us Page - Raw stored currency from localStorage:", storedCurrencyRaw);

        let storedCurrency = null;
        if (storedCurrencyRaw) {
            const tempCurrency = storedCurrencyRaw.trim().toUpperCase();
            if (tempCurrency.startsWith('"') && tempCurrency.endsWith('"')) {
                storedCurrency = tempCurrency.slice(1, -1);
                console.log("DEBUG: About Us Page - From localStorage - removed quotes, processed:", storedCurrency);
            } else {
                storedCurrency = tempCurrency;
            }
        }
        console.log("DEBUG: About Us Page - From localStorage - processed (after quote check):", storedCurrency);

        let effectiveCurrency = 'NGN';
        if (currencyFromURL && window.currencyExchangeRates && window.currencyExchangeRates[currencyFromURL]) {
            effectiveCurrency = currencyFromURL;
            console.log("DEBUG: About Us Page - Currency set from URL:", effectiveCurrency);
        } else if (storedCurrency && window.currencyExchangeRates && window.currencyExchangeRates[storedCurrency]) {
            effectiveCurrency = storedCurrency;
            console.log("DEBUG: About Us Page - Currency set from localStorage:", effectiveCurrency);
        } else {
            console.log("DEBUG: About Us Page - No valid currency found in storage or URL. Using default NGN.");
        }
        window.currentCurrency = effectiveCurrency;
        console.log("About Us Page: Initial window.currentCurrency (Final):", window.currentCurrency);

        // Cart functionality (simplified for this page, assumes no dynamic cart updates from other pages)
        const cartCountSpan = document.querySelector('.cart-count');
        const miniCartDropdown = document.getElementById('miniCartDropdown');
        const miniCartItemCount = document.getElementById('miniCartItemCount');
        const miniCartItemsContainer = document.getElementById('miniCartItems');
        const miniCartTotalSpan = document.getElementById('miniCartTotal');

        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        function updateCartDisplay() {
            cartCountSpan.textContent = cart.length;
            miniCartItemCount.textContent = cart.length;

            if (cart.length > 0) {
                cartCountSpan.style.display = 'flex';
            } else {
                cartCountSpan.style.display = 'none';
            }

            miniCartItemsContainer.innerHTML = '';

            if (cart.length === 0) {
                miniCartItemsContainer.innerHTML = '<p class="empty-cart-message">Your cart is empty.</p>';
                miniCartTotalSpan.textContent = formatPrice(0);
            } else {
                let total = 0;
                cart.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.classList.add('mini-cart-item');

                    const imageUrl = item.imageSrc || 'https://placehold.co/50x50/f0f0f0/cccccc?text=No+Image';

                    const priceValue = parseFloat(item.basePriceNgn);
                    const displayPrice = formatPrice(priceValue);

                    itemElement.innerHTML = `
                        <img src="${imageUrl}" alt="${item.name}" onerror="this.onerror=null;this.src='https://placehold.co/50x50/f0f0f0/cccccc?text=No+Image';">
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">${displayPrice}</div>
                        </div>
                    `;
                    miniCartItemsContainer.appendChild(itemElement);

                    if (!isNaN(priceValue)) {
                        total += priceValue;
                    }
                });
                miniCartTotalSpan.textContent = formatPrice(total);
            }
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        // Initial cart display update
        updateCartDisplay();
        updateAllPrices(); // Call this after currency is set and cart is loaded

        // Mobile Menu Toggle (copied for consistency)
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileMenuClose = document.querySelector('.mobile-menu-close');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.add('active');
                mobileMenuOverlay.classList.add('active');
            });
        }

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
            });
        }

        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', (event) => {
                if (event.target === mobileMenuOverlay) {
                    mobileMenu.classList.remove('active');
                    mobileMenuOverlay.classList.remove('active');
                }
            });
        }

        // Mobile Categories Dropdown (copied for consistency)
        const mobileCategoryItems = document.querySelectorAll('.mobile-category-item.has-subcategories');
        mobileCategoryItems.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
                item.classList.toggle('active');
                let subcategories = item.nextElementSibling;
                if (subcategories && subcategories.classList.contains('mobile-subcategories')) {
                    subcategories.classList.toggle('active');
                }
            });
        });

        // Mobile Search Toggle (copied for consistency)
        const mobileSearchToggleBtn = document.querySelector('.mobile-search-toggle-btn');
        const header = document.querySelector('.header');
        const mobileSearchBar = document.getElementById('mobileSearchBar');

        if (mobileSearchToggleBtn && header && mobileSearchBar) {
            mobileSearchToggleBtn.addEventListener('click', () => {
                const isSearchActive = header.classList.toggle('mobile-search-active');
                mobileSearchToggleBtn.setAttribute('aria-expanded', isSearchActive);

                if (isSearchActive) {
                    mobileSearchBar.querySelector('.search-input').focus();
                }
            });
        }

        // Info Dropdown Toggle (copied for consistency)
        const infoDropdownBtn = document.querySelector('.info-dropdown-btn');
        const infoDropdownMenu = document.getElementById('info-dropdown-menu');

        if (infoDropdownBtn && infoDropdownMenu) {
            infoDropdownBtn.addEventListener('click', (event) => {
                event.stopPropagation();
                const isExpanded = infoDropdownMenu.classList.toggle('active');
                infoDropdownBtn.setAttribute('aria-expanded', isExpanded);
            });

            document.addEventListener('click', (event) => {
                if (!infoDropdownMenu.contains(event.target) && !infoDropdownBtn.contains(event.target)) {
                    infoDropdownMenu.classList.remove('active');
                    infoDropdownBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>
@endsection
