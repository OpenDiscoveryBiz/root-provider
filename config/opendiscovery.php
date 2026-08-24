<?php

return [
    'providers' => [
        'DK' => env('PROVIDER_DK', 'https://dk.opendiscovery.biz'),
    ],

    'root_ttl' => (int) env('ROOT_TTL', 3600),
];
