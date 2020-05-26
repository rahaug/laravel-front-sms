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

    'notifiable_phone_key' => env('FRONT_NOTIFIABLE_KEY', 'phone')
];
