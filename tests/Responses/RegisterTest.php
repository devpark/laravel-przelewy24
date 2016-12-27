<?php

namespace Tests\Responses;

use Tests\UnitTestCase;
use Devpark\Transfers24\Responses\Register as ResponseRegister;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;
use Mockery as m;

class RegisterTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->handler = m::mock(HandlerTransfers24::class)->makePartial();
        $this->response = new ResponseRegister($this->handler);
    }

    /** @test */
    public function verified_return_token()
    {
        $token_handler = '123456789';
        $this->handler->shouldReceive('getToken')->andReturn($token_handler);

        $token = $this->response->getToken();
        $this->assertEquals($token, $token_handler);
    }
}
