<?php

return [

    /*
     * You may wish for all e-mails sent with Mailgun to be sent from
     * the same address. Here, you may specify a name and address that is
     * used globally for all e-mails that are sent by Mailgun.
     *
     */
    'from' => [
        'address' => env('MAILGUN_FROM_ADDRESS', 'notifications@solumails.com'),
        'name'    => env('MAILGUN_FROM_NAME', 'Solumails'),
    ],

    /*
     * Global reply-to e-mail address
     *
     */
    'reply_to' => env('MAILGUN_REPLY_TO', 'notifications@solumails.com'),

    /*
     * Mailgun (private) API key
     *
     */
    'api_key' => 'key-8384bc97dca8f049aabdf9be140c85ae',

    /*
     * Mailgun public API key
     *
     */
    'public_api_key' => 'pubkey-686014f7801864792f0f84f2dd0c5065',

    /*
     * Domain name registered with Mailgun
     *
     */
    'domain' => env('MAILGUN_DOMAIN', 'solumails.com'),

    /*
     * Force the from address
     *
     * When your `from` e-mail address is not from the domain specified some
     * e-mail clients (Outlook) tend to display the from address incorrectly
     * By enabling this setting Mailgun will force the `from` address so the
     * from address will be displayed correctly in all e-mail clients.
     *
     * Warning:
     * This parameter is not documented in the Mailgun documentation
     * because if enabled, Mailgun is not able to handle soft bounces
     *
     */
    'force_from_address' => env('MAILGUN_FORCE_FROM_ADDRESS', false),

    /*
     * Testing
     *
     * Catch All address
     *
     * Specify an email address that receives all emails send with Mailgun
     * This email address will overwrite all email addresses within messages
     */
    'catch_all' => env('MAILGUN_CATCH_ALL', ''),

    /*
     * Testing
     *
     * Mailgun's testmode
     *
     * Send messages in test mode by setting this setting to true.
     * When you do this, Mailgun will accept the message but will
     * not send it. This is useful for testing purposes.
     *
     * Note: Mailgun does charge your account for messages sent in test mode.
     */
    'testmode' => env('MAILGUN_TEST_MODE', false),
];
