<?php

namespace Tests\Responses;

use Tests\UnitTestCase;
use Devpark\Transfers24\Responses\Verify as ResponseVerify;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;
use Mockery as m;

class VerifyTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->handler = m::mock(HandlerTransfers24::class)->makePartial();
        $this->response = new ResponseVerify($this->handler);
    }

    /** @test */
    public function check_same_response_parameters()
    {
        $parameters_handler = [
            'a' => 'a',
            'b' => 'b',
        ];
        $this->handler->shouldReceive('getReceiveParameters')->andReturn($parameters_handler);

        $parameters = $this->response->getReceiveParameters();
        $this->assertEquals($parameters, $parameters_handler);
    }

    /** @test */
    public function check_same_order_id()
    {
        $order_id_handler = '123456789';
        $this->handler->shouldReceive('getOrderId')->andReturn($order_id_handler);

        $order_id = $this->response->getOrderId();
        $this->assertEquals($order_id, $order_id_handler);
    }
}
