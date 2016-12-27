<?php

namespace Tests\Responses;

use Tests\UnitTestCase;
use Devpark\Transfers24\Responses\Response;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;
use Mockery as m;

class ResponseTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $reflection = new \ReflectionClass(Response::class);
        $transfers24_property = $reflection->getProperty('transfers24');
        $transfers24_property->setAccessible(true);

        $this->handler = m::mock(HandlerTransfers24::class)->makePartial();
        $this->response = m::mock(Response::class)->makePartial();
        $transfers24_property->setValue($this->response, $this->handler);
    }

    /** @test */
    public function check_response_success()
    {
        $this->handler->shouldReceive('getCode')->once()->andReturn('0');

        $success = $this->response->isSuccess();
        $this->assertTrue($success);
    }

    /** @test */
    public function check_correct_error_code_passing()
    {
        $code = 4;
        $this->handler->shouldReceive('getCode')->andReturn($code);

        $error_code = $this->response->getErrorCode();
        $this->assertEquals($error_code, $code);
    }

    /** @test */
    public function check_correct_error_description_passing()
    {
        $error_description = [
            'e1' => 'error 1 desc',
            'e2' => 'error 2 desc',
        ];
        $this->handler->shouldReceive('getErrorDescription')->once()->andReturn($error_description);

        $error_description_passing = $this->response->getErrorDescription();
        $this->assertEquals($error_description_passing, $error_description);
    }

    /** @test */
    public function check_correct_request_paramters_passing()
    {
        $parameters_request = [
            'a' => 'a',
            'b' => 'b',
        ];
        $this->handler->shouldReceive('getRequestParameters')->once()->andReturn($parameters_request);

        $parameters = $this->response->getRequestParameters();
        $this->assertEquals($parameters, $parameters_request);
    }

    /** @test */
    public function check_correct_session_id_passing()
    {
        $session_id = 4;
        $this->handler->shouldReceive('getSessionId')->once()->andReturn($session_id);

        $passing_session_id = $this->response->getSessionId();
        $this->assertEquals($passing_session_id, $session_id);
    }
}
