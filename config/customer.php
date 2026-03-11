<?php

return [

    // PARAMETERS
    'send_mail' => false,
    'dependants' => false,
    'custom_successful_payment' => true,
    'enable_test' => env('ENABLE_TESTING', 0),
    'fields' => [
        'city'=> true,
        'address'=> true,
        'coordinates'=> true,
        'password'=> true,
        'age'=> false,
        'shirt'=> false,
        'shirt_size'=> false,
        'invoice_data'=> true,
        'emergency_short'=> false,
        'emergency_long'=> false,
    ],
    'custom' => [
        'register'=> true, // Habilitar registro
        'custom_register'=> false, // Habilitar registro totalmente personalizado
        'register_rules'=> false, // Haiblitar reglas de registro personalizadas
        'after_register'=> false, // Habilitar funcion luego de registro
        'after_login'=> false, // Habilitar funcion luego de login
        'after_succesful_payment'=> false, // Habilitar funcion luego de pago
    ],

    // API
    'api_slave' => false,
    'api_master' => false,
    'main_server_url' => 'http://master.test/customer-api-server/',
    'main_server_app_key' => '61b7109893d07a55bccb86e6a5817a1cb9ad5c6d',

];