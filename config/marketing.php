<?php

return [
    'enabled' => env('MARKETING_EMAIL_ENABLED', false),
    'test_enabled' => env('MARKETING_TEST_EMAIL_ENABLED', false),
    'from' => ['address' => 'promotions@albertinang.com', 'name' => 'AlbertinaNG'],
    'reply_to' => 'promotions@albertinang.com',
    // Separate from order mail. Configure this mailer without changing transaction email settings.
    'mailer' => env('MARKETING_MAILER', 'marketing'),
    'batch_limit' => (int) env('MARKETING_BATCH_LIMIT', 50),
];
