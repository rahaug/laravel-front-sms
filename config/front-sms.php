<?php

return [
    // Unique customer ID
    'serviceId' => env('FRONT_SERVICE_ID', null),
    // Unique sender ID
    // An assigned number / serial
    //number of Front or assigned
    //sender text, maximum 11
    //characters.
    'fromId' => env('FRONT_FROM_ID', null),

    'notifiablePhoneKey' => env('FRONT_NOTIFIABLE_KEY', 'phone'),
    'defaultRegion' => null // ISO 3166-2 Geographic Region code (E.g. GB, NO, SE)
];
