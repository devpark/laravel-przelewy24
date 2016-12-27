<?php

namespace Tests;

use Devpark\Transfers24\Currency;

class CurrencyTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function is_corrent_array_if_currencies()
    {
        $currencies = [
            'PLN' => 'PLN',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
            'CZK' => 'CZK',
        ];

        $avalable_currencies = Currency::getCurrencies();
        $this->assertEquals($avalable_currencies, $currencies);
    }

    /** @test */
    public function set_one_from_avalible_currencies()
    {
        $currency = 'GBP';
        $pass_currency = Currency::get($currency);
        $this->assertEquals($pass_currency, $currency);

        $default_currency = 'PLN';
        $currency = 'AED';
        $pass_currency = Currency::get($currency);
        $this->assertEquals($pass_currency, $default_currency);
    }
}
