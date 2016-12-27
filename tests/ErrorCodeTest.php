<?php

namespace Tests;

use Devpark\Transfers24\ErrorCode;

class ErrorCodeTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function get_description_of_code_if_exists()
    {
        $name = 'err58';
        $pass_description = ErrorCode::getDescription($name);
        $this->assertEquals($pass_description, 'Incorrect sequence number !');

        $name = 'unknown_error';
        $pass_description = ErrorCode::getDescription($name);
        $this->assertNull($pass_description);
    }
}
