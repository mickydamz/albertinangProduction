<?php

// One navigation directory shared by desktop, mobile and the dashboard.
return [
    [
        'id' => "orders", 'label' => "Orders", 'icon' => "shopping-bag",
        'description' => "Manage purchases from payment to fulfilment.",
        'items' => [
            ['route' => "admin.orders.index", 'label' => "All orders", 'matches' => ["admin.orders.*"], 'icon' => "list-ul", 'description' => "View payments and update delivery or collection progress."],
            ['route' => "admin.cancellations.index", 'label' => "Cancellations", 'matches' => ["admin.cancellations.*"], 'icon' => "ban", 'description' => "Review cancellation requests and refunds before shipment or collection."],
            ['route' => "admin.returns.index", 'label' => "Returns", 'matches' => ["admin.returns.*"], 'icon' => "rotate-left", 'description' => "Handle return requests and refunds after delivery or collection."],
        ],
    ],
    [
        'id' => "products", 'label' => "Products & suppliers", 'icon' => "box-open",
        'description' => "Manage your catalogue and the people supplying it.",
        'items' => [
            ['route' => "admin.products.index", 'label' => "Products", 'matches' => ["admin.products.*"], 'icon' => "box", 'description' => "Add products, manage stock and edit listings."],
            ['route' => "admin.categories.index", 'label' => "Categories", 'matches' => ["admin.categories.*"], 'icon' => "folder-open", 'description' => "Organise the main product categories."],
            ['route' => "admin.subcategories.index", 'label' => "Subcategories", 'matches' => ["admin.subcategories.*"], 'icon' => "folder", 'description' => "Group products within a category."],
            ['route' => "admin.brands.index", 'label' => "Brands", 'matches' => ["admin.brands.index", "admin.brands.create", "admin.brands.edit", "admin.brands.show"], 'icon' => "copyright", 'description' => "Manage product brands and their details."],
            ['route' => "admin.suppliers.index", 'label' => "Suppliers", 'matches' => ["admin.suppliers.*"], 'icon' => "truck-loading", 'description' => "Manage supplier records."],
            ['route' => "admin.brands.assign", 'label' => "Brand assignments", 'matches' => ["admin.brands.assign"], 'icon' => "handshake", 'description' => "Assign brands to team members."],
            ['route' => "admin.tags.index", 'label' => "Product tags", 'matches' => ["admin.tags.*"], 'icon' => "hashtag", 'description' => "Label products for discovery."],
            ['route' => "admin.colors.index", 'label' => "Colours", 'matches' => ["admin.colors.*"], 'icon' => "palette", 'description' => "Manage available product colours."],
            ['route' => "admin.sizes.index", 'label' => "Sizes", 'matches' => ["admin.sizes.*"], 'icon' => "ruler-combined", 'description' => "Manage available product sizes."],
        ],
    ],
    [
        'id' => "pricing", 'label' => "Pricing & promotions", 'icon' => "tags",
        'description' => "Control selling prices and promotional offers.",
        'items' => [
            ['route' => "admin.markup.index", 'label' => "Price markup", 'matches' => ["admin.markup.*"], 'icon' => "percent", 'description' => "Set price margins and markup rules."],
            ['route' => "admin.discount.index", 'label' => "Discounts", 'matches' => ["admin.discount.*"], 'icon' => "scissors", 'description' => "Manage product discounts."],
            ['route' => "admin.coupons.index", 'label' => "Coupon codes", 'matches' => ["admin.coupons.*"], 'icon' => "ticket", 'description' => "Create and manage checkout offers."],
            ['route' => "admin.affiliates.index", 'label' => "Affiliates", 'matches' => ["admin.affiliates.*"], 'icon' => "share-nodes", 'description' => "Manage your referral partners."],
        ],
    ],
    [
        'id' => "delivery", 'label' => "Delivery & pickup", 'icon' => "truck",
        'description' => "Configure destinations, collection points and charges.",
        'items' => [
            ['route' => "admin.states.index", 'label' => "States", 'matches' => ["admin.states.*"], 'icon' => "map", 'description' => "Manage the states you serve."],
            ['route' => "admin.cities.index", 'label' => "Cities", 'matches' => ["admin.cities.*"], 'icon' => "city", 'description' => "Manage delivery cities."],
            ['route' => "admin.locations.index", 'label' => "Delivery areas", 'matches' => ["admin.locations.*"], 'icon' => "location-dot", 'description' => "Set delivery destinations and areas."],
            ['route' => "admin.pickup-points.index", 'label' => "Pickup points", 'matches' => ["admin.pickup-points.*"], 'icon' => "store", 'description' => "Set collection points and addresses."],
            ['route' => "admin.store-locations.index", 'label' => "Store locations", 'matches' => ["admin.store-locations.*"], 'icon' => "map-marker-alt", 'description' => "Manage physical store locations."],
            ['route' => "admin.shipping.index", 'label' => "Shipping charges", 'matches' => ["admin.shipping.*"], 'icon' => "truck", 'description' => "Configure shipping options and charges."],
            ['route' => "admin.weight.index", 'label' => "Weight-based charges", 'matches' => ["admin.weight.*"], 'icon' => "weight-hanging", 'description' => "Configure delivery pricing by weight."],
        ],
    ],
    [
        'id' => "customers", 'label' => "Customers & support", 'icon' => "users",
        'description' => "Manage accounts, feedback and customer conversations.",
        'items' => [
            ['route' => "admin.users.index", 'label' => "Customer & team accounts", 'matches' => ["admin.users.*"], 'icon' => "users", 'description' => "Manage customers and staff accounts."],
            ['route' => "admin.reviews.index", 'label' => "Product reviews", 'matches' => ["admin.reviews.*"], 'icon' => "star", 'description' => "Manage customer product feedback."],
            ['route' => "admin.tickets.index", 'label' => "Support tickets", 'matches' => ["admin.tickets.*"], 'icon' => "headset", 'description' => "Track customer questions and problems."],
            ['route' => "admin.send.email.form", 'label' => "Send an email", 'matches' => ["admin.send.email.*"], 'icon' => "envelope", 'description' => "Compose a customer email."],
        ],
    ],
    [
        'id' => "finance", 'label' => "Payments & invoices", 'icon' => "credit-card",
        'description' => "Find transactions and configure billing.",
        'items' => [
            ['route' => "admin.paystack-transactions.index", 'label' => "Paystack transactions", 'matches' => ["admin.paystack-transactions.*"], 'icon' => "credit-card", 'description' => "Inspect Paystack payment records."],
            ['route' => "admin.transactions.index", 'label' => "Other transactions", 'matches' => ["admin.transactions.*"], 'icon' => "receipt", 'description' => "Manage other transaction records."],
            ['route' => "admin.payment-methods.index", 'label' => "Payment methods", 'matches' => ["admin.payment-methods.*"], 'icon' => "wallet", 'description' => "Manage payment options."],
            ['route' => "admin.currencies.index", 'label' => "Currencies & rates", 'matches' => ["admin.currencies.*"], 'icon' => "coins", 'description' => "Manage currencies and exchange rates."],
            ['route' => "admin.invoice.settings", 'label' => "Invoice settings", 'matches' => ["admin.invoice.settings*"], 'icon' => "file-invoice", 'description' => "Configure invoice branding and details."],
        ],
    ],
    [
        'id' => "content", 'label' => "Website content", 'icon' => "file-alt",
        'description' => "Keep the public store pages up to date.",
        'items' => [
            ['route' => "admin.banners.index", 'label' => "Store banners", 'matches' => ["admin.banners.*"], 'icon' => "images", 'description' => "Manage promotional images on the website."],
            ['route' => "admin.about.index", 'label' => "About us", 'matches' => ["admin.about.*"], 'icon' => "circle-info", 'description' => "Edit the company information page."],
            ['route' => "admin.contact.index", 'label' => "Contact details", 'matches' => ["admin.contact.*"], 'icon' => "address-book", 'description' => "Edit public contact details."],
            ['route' => "admin.faqs.index", 'label' => "Frequently asked questions", 'matches' => ["admin.faqs.*"], 'icon' => "circle-question", 'description' => "Manage answers to common questions."],
        ],
    ],
    [
        'id' => "settings", 'label' => "Store & administration", 'icon' => "gear",
        'description' => "Store configuration and administrative history.",
        'items' => [
            ['route' => "admin.settings.index", 'label' => "Store settings", 'matches' => ["admin.settings.*"], 'icon' => "sliders", 'description' => "Configure the store and integrations."],
            ['route' => "admin.audit.index", 'label' => "Activity log", 'matches' => ["admin.audit.*"], 'icon' => "clock-rotate-left", 'description' => "Review recorded administrative changes."],
        ],
    ],
];
