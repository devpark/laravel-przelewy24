<?php

return [

    /*
     * Przelewy24 api version
     */
    'version' => '3.2',

    'merchant_id' => env('PRZELEWY24_MERCHANT_ID'),
    'pos_id' => env('PRZELEWY24_POS_ID'),
    'crc' => env('PRZELEWY24_CRC'),
    'test_server' => env('PRZELEWY24_TEST_SERVER', false),
    'url_return' => env('PRZELEWY24_URL_RETURN', 'transfers24/callback'),
    'url_status' => env('PRZELEWY24_URL_STATUS', 'transfers24/status'),
];
