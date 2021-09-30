<?php

namespace Tests\Responses;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Responses\Verify as ResponseVerify;
use Devpark\Transfers24\Services\DecodedBody;
use Mockery as m;
use Tests\UnitTestCase;

class VerifyTest extends UnitTestCase
{
    /**
     * @var ResponseVerify
     */
    private $response;

    /**
     * @var m\MockInterface
     */
    private $decoded_body;

    /**
     * @var m\MockInterface
     */
    private $form;

    protected function setUp()
    {
        parent::setUp();

        $this->form = m::mock(Form::class);
        $this->decoded_body = m::mock(DecodedBody::class);

        $this->response = new ResponseVerify($this->form, $this->decoded_body);
    }

    /** @test */
    public function check_same_response_parameters()
    {
        $parameters_handler = [
            'a' => 'a',
            'b' => 'b',
        ];
        $this->form->shouldReceive('getReceiveParameters')->andReturn($parameters_handler);

        $parameters = $this->response->getReceiveParameters();
        $this->assertEquals($parameters, $parameters_handler);
    }

    /** @test */
    public function check_same_order_id()
    {
        $order_id_handler = '123456789';
        $this->form->shouldReceive('getOrderId')->andReturn($order_id_handler);

        $order_id = $this->response->getOrderId();
        $this->assertEquals($order_id, $order_id_handler);
    }
}
