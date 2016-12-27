<?php

namespace Tests\Services;

use Tests\UnitTestCase;
use Devpark\Transfers24\Services\Amount;

class CountryTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->amount = new Amount();
    }

    /** @test */
    public function check_correct_normalize_amount()
    {
        $amount = 123;
        $pass_amount = Amount::get($amount);
        $this->assertEquals($pass_amount, 12300);

        $amount = '123.45';
        $pass_amount = Amount::get($amount);
        $this->assertEquals($pass_amount, 12345);

        $amount = '123,45';
        $pass_amount = Amount::get($amount);
        $this->assertEquals($pass_amount, 12345);

        $amount = 'asdf';
        $pass_amount = Amount::get($amount);
        $this->assertEquals($pass_amount, 0);

        $amount = '0,01';
        $pass_amount = Amount::get($amount);
        $this->assertSame($pass_amount, 1);

        $amount = 001;
        $pass_amount = Amount::get($amount);
        $this->assertSame($pass_amount, 100);

        $amount = 001.11;
        $pass_amount = Amount::get($amount);
        $this->assertSame($pass_amount, 111);
    }
}
