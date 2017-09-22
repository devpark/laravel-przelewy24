<?php

namespace Tests;

use Devpark\Transfers24\Encoding;

class EncodingTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function set_one_from_avalible_encoding()
    {
        $encoding = 'UTF';
        $pass_encoding = Encoding::get($encoding);
        $this->assertEquals($pass_encoding, 'UTF-8');

        $encoding = 'UTF-8';
        $pass_encoding = Encoding::get($encoding);
        $this->assertEquals($pass_encoding, 'UTF-8');

        $default_encoding = 'ISO-8859-2';
        $encoding = 'other';
        $pass_encoding = Encoding::get($encoding);
        $this->assertEquals($pass_encoding, $default_encoding);

        $encoding = 'utf';
        $pass_encoding = Encoding::get($encoding);
        $this->assertEquals($pass_encoding, 'UTF-8');
    }
}
