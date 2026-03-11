<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'froala' => [
        'key' => env('FROALA_KEY'),
    ],

    'mailgun' => [
        'domain' => env('MAIL_DOMAIN'),
        'secret' => env('MAIL_SECRET'),
    ],
    
    'mandrill' => [
        'secret' => '',
    ],

    'aws' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_REGION'),
    ],
    
    'ses' => [
        'key'    => env('AWS_SES_KEY'),
        'secret' => env('AWS_SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => '',
        'secret' => '',
    ],

    'facebook' => [
      'client_id' => env('FACEBOOK_CLIENT_ID'),
      'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
      'redirect' => env('FACEBOOK_LOGIN_REDIRECT')
    ],

    'google' => [
      'client_id' => env('GOOGLE_CLIENT_ID'),
      'client_secret' => env('GOOGLE_CLIENT_SECRET'),
      'redirect' => env('GOOGLE_LOGIN_REDIRECT')
    ],

    'instagram' => [
        'client_id' => 'your-github-app-id',
        'client_secret' => 'your-github-app-secret',
        'redirect' => 'http://your-callback-url',
    ],

    'rss_sources' => [
      'http://www.eldia.com.bo/rss.php', 
      'http://www.la-razon.com/rss/nacional/',
      'http://rss.eldiario.net/nacional.php',
      'http://www.lostiempos.com/rss/lostiempos-ultimas.xml',
      'http://www.laprensa.com.bo/rss/laprensa-titulares.xml',
      'http://www.jornadanet.com/rss/Portada.xml',
      'http://feeds.feedburner.com/ErnestoJustiniano',
      'http://www3.abi.bo/rss/abi.xml',
      'http://www.hoybolivia.com/rss.php'
    ],

    'enable_test' => env('ENABLE_TEST', 1), // Habilitar cobros de 1Bs por detras.

    'rabbit_mq' => [
        'rabbit_host'     => env('RABBIT_HOST', 'localhost'),
        'rabbit_port'     => env('RABBIT_PORT', 5672),
        'rabbit_user'     => env('RABBIT_USER', 'myuser'),
        'rabbit_password' => env('RABBIT_PASSWORD', 'mypassword'),
    ],

    'node_server' => [
        'host'     => env('SERVER_NODE', 'https://taxisapp-backend-prod.solunes.site'),
    ]

];
