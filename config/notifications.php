<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Automated system notification channels
    |--------------------------------------------------------------------------
    |
    | Default channel list for portal-generated alerts (class schedule, invoices,
    | attendance, leave decisions). Email can be enabled when mail is configured.
    |
    */
    'system_channels' => array_values(array_filter(array_map('trim', explode(',', (string) env('SYSTEM_NOTIFICATION_CHANNELS', 'database'))))),

    'system_email_enabled' => (bool) env('SYSTEM_NOTIFICATION_EMAIL', false),

    'whatsapp' => [
        'enabled' => (bool) env('WHATSAPP_NOTIFICATIONS_ENABLED', false),
        'log_only' => (bool) env('WHATSAPP_LOG_ONLY', true),
    ],

];
