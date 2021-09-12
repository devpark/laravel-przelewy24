<?php

return [

    'merchant_id' => env('PRZELEWY24_MERCHANT_ID'),
    'pos_id' => env('PRZELEWY24_POS_ID'),
    'crc' => env('PRZELEWY24_CRC'),
    'test_server' => env('PRZELEWY24_TEST_SERVER', false),
    'url_return' => env('PRZELEWY24_URL_RETURN', 'transfers24/callback'),
    'url_status' => env('PRZELEWY24_URL_STATUS', 'transfers24/status'),

    /*
     * Enable credentials scope per merchant.
     * The package could be used by Saas and could provide separate payment service for every Merchant
     */
    'credentials-scope' => false,

    /**
     * Time limit for transaction process, 0 - no limit, max. 99 (in minutes)
     */
    'time-limit' => 0,
    /**
     * Parameter determines wheter a user should wait for result of the transaction in the transaction service and be redirected back to the shop upon receiving confirmation or be redirected back to the shop immediately after payment
     */
    'wait-for-result' => false,
    /**
     * Acceptance of Przelewy24 regulations:
     * false – display consent on p24 website (default),
     * true – consent granted, do not display.
     */
    'regulation-accept' => false,
    /**
     * Coding system for characters sent: ISO-8859-2, UTF-8, Windows-1250
     */
    'encoding' => 'UTF-8',
];
