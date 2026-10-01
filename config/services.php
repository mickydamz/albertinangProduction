<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
    'paystack' => [
    'secret' => env('PAYSTACK_SECRET_KEY'),
    'public' => env('PAYSTACK_PUBLIC_KEY'),
],
'stripe' => [
    'secret' => env('STRIPE_SECRET_KEY'),
    'public' => env('STRIPE_PUBLISHABLE_KEY'),
    // NGN -> USD rate used to compute the Stripe charge amount. Kept server-side
    // so the browser cannot dictate the amount; the checkout page reads the same
    // value for its display so client and server always agree.
    'usd_rate' => env('STRIPE_USD_RATE', 0.00067),
],
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],
    'turnstile' => [
    'site_key'   => env('TURNSTILE_SITE_KEY'),
    'secret_key' => env('TURNSTILE_SECRET_KEY'),
],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
