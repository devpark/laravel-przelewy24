<?php

namespace Tests\Services;

use Tests\UnitTestCase;
use Devpark\Transfers24\CodeTranslate;

class CodeTranslateTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function is_correct_code_array()
    {
        $codes = [
            'CODE' => '-1',
        ];

        $avalable_codes = CodeTranslate::getCodes();

        $this->assertEquals($avalable_codes, $codes);
    }

    /** @test */
    public function set_one_from_avalible_codes()
    {
        $default_code = '999';

        $code = '-1';
        $pass_code = CodeTranslate::getCode($code, $default_code);
        $this->assertEquals($pass_code, $code);

        $code = 'CODE';
        $pass_code = CodeTranslate::getCode($code, $default_code);
        $this->assertEquals($pass_code, '-1');

        $code = 'code';
        $pass_code = CodeTranslate::getCode($code, $default_code);
        $this->assertEquals($pass_code, '-1');

        $code = '111';
        $pass_code = CodeTranslate::getCode($code, $default_code);
        $this->assertEquals($pass_code, $default_code);
    }
}
