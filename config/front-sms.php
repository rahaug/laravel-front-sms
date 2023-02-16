<?php

return [
    // Output SMS in log for debugging purposes
    'fakeMessages' => env('FRONT_FAKE_MESSAGES', true),
    // Unique customer ID
    'serviceId' => env('FRONT_SERVICE_ID', null),
    // Unique sender ID
    // An assigned number / serial
    //number of Front or assigned
    //sender text, maximum 11
    //characters.
    'fromId' => env('FRONT_SENDER_ID', null),

    // Password for Authentication (if you're not authenticating through IP)
    'password' => env('FRONT_PASSWORD', null),

    'notifiablePhoneKey' => env('FRONT_NOTIFIABLE_KEY', 'phone'),
    'defaultRegion' => env('FRONT_DEFAULT_REGION', null) // ISO 3166-2 Geographic Region code (E.g. GB, NO, SE)
];
