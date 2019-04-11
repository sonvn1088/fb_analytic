<?php

return [
    'graph' => 'https://graph.facebook.com/v3.2/',
    'cdn' => 'https://scontent.xx.fbcdn.net/',
    'token_server' => '34.80.15.3',
    'step_time' => 10,
    'token' => env('TOKEN'),
    'pages' => [],
    'api' => [
        'iphone' => [
            'key' => '3e7c78e35a76a9299309885393b02d97',
            'secret' => 'c1e620fa708a1d5696fb991c1bde5662',
        ],
        'ipad' => [
            'key' => 'f0c9c86c466dc6b5acdf0b35308e83d1',
            'secret' => '7c036d47372dd5f2df27bfe76d4ae0c4',
        ],
        'android' => [
            'key' => '62f8ce9f74b12f84c123cc23437a4a32',
            'secret' => '882a8490361da98702bf97a021ddc14d',
        ],

        'base_url' => 'https://api.facebook.com/restserver.php',
        'agent' => '[FBAN/FB4A;FBAV/35.0.0.48.273;FBDM/{density=1.33125,width=800,height=1205};FBLC/en_US;FBCR/;FBPN/com.facebook.katana;FBDV/Nexus 7;FBSV/4.1.1;FBBK/0;]',
    ]
];

