<?php

namespace Tests\Services\Handlers;

use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;
use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Devpark\Transfers24\Responses\Http\Response as HttpResponse;
use Tests\UnitTestCase;
use Mockery as m;

class Transfers24Test extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->gateway = m::mock(GatewayTransfers24::class)->makePartial();
        $this->handler = new HandlerTransfers24($this->gateway);
        $this->http_response = new HttpResponse();

        $this->payment_request_params = [
            'p24_session_id' => '5852fe8ca260c',
            'p24_amount' => '10000',
            'p24_currency' => 'PLN',
            'p24_description' => 'Example description',
            'p24_email' => 'test@test.pl',
            'p24_client' => 'last name',
            'p24_address' => 'Poznanska 20',
            'p24_zip' => '62-021',
            'p24_city' => 'Poznan',
            'p24_country' => 'PL',
            'p24_phone' => '77777777',
            'p24_language' => 'pl',
            'p24_method' => '1',
            'p24_url_return' => 'transfers24/callback',
            'p24_url_status' => 'transfers24/status',
            'p24_channel' => '1',
            'p24_name_1' => 'ProductName',
            'p24_description_1' => 'ProductDescription',
            'p24_quantity_1' => '10',
            'p24_price_1' => '1000',
            'p24_number_1' => '1234',
        ];

        $this->http_answer = [
            'status' => 200,
            'body' => 'error=4&errorMessage=field1:desc1&field1:desc2',
            'params' => [
                'p24_merchant_id' => '212194',
                'p24_pos_id' => '212194',
                'p24_api_version' => '3.2',
                'p24_session_id' => '5852fe8ca260c',
                'p24_amount' => '10000',
                'p24_currency' => 'PLN',
                'p24_description' => 'Example description',
                'p24_email' => 'test@test.pl',
                'p24_client' => 'last name',
                'p24_address' => 'Poznanska 20',
                'p24_zip' => '62-021',
                'p24_city' => 'Poznan',
                'p24_country' => 'PL',
                'p24_phone' => '77777777',
                'p24_language' => 'pl',
                'p24_method' => '1',
                'p24_url_return' => 'transfers24/callback',
                'p24_url_status' => 'transfers24/status',
                'p24_channel' => '1',
                'p24_name_1' => 'ProductName',
                'p24_description_1' => 'ProductDescription',
                'p24_quantity_1' => '10',
                'p24_price_1' => '1000',
                'p24_number_1' => '1234',
                'p24_sign' => 'aba480b770f78cef1bb6c02529218dd1',
            ],
       ];

        $this->post_data = [
            'p24_merchant_id' => '123456789',
            'p24_pos_id' => '123456789',
            'p24_session_id' => '123456789',
            'p24_amount' => '123',
            'p24_currency' => 'PLN',
            'p24_order_id' => '123456789',
            'p24_method' => '1',
            'p24_statement' => 'test',
            'p24_sign' => '123456789',
        ];
    }

    /** @test */
    public function check_request_params_same_with_response_from_transfers24()
    {
        $this->http_response->addFormParams($this->http_answer['params']);
        $this->http_response->addStatusCode($this->http_answer['status']);
        $this->http_response->addBody($this->http_answer['body']);

        $this->gateway->shouldReceive('trnRegister')->andReturn($this->http_response);
        $this->response = $this->handler->init($this->payment_request_params);

        $this->assertEquals($this->response->getRequestParameters(), $this->http_response->getFormParams());
    }

    /** @test */
    public function test_execution_payment()
    {
        $this->gateway->shouldReceive('trnRequest')->andReturn('http://redirect');
        $response = $this->handler->execute('123456789');
        $this->assertEquals($response, 'http://redirect');
    }

    /** @test */
    public function check_convert_segment_to_description()
    {
        $reflection = new \ReflectionClass(HandlerTransfers24::class);
        $handler_method = $reflection->getMethod('segmentToDescription');
        $handler_method->setAccessible(true);

        $handler = m::mock(HandlerTransfers24::class)->makePartial();

        $error_array = [
            'p24_url_status' => 'Incorrect_URL',
        ];

        $handler->segmentToDescription('p24_url_status:Incorrect_URL');
        $this->assertEquals($handler->getErrorDescription(), $error_array);
    }

    /** @test */
    public function test_no_code_and_no_messsage_and_no_token_from_transfers24()
    {
        $this->refreshHandler();
        $this->http_answer['body'] = '';
        $this->http_response->addBody($this->http_answer['body']);
        $this->handler->convertResponse();
        $this->assertNull($this->handler->getCode());
        $this->assertNull($this->handler->getToken());
        $this->assertEquals($this->handler->getErrorDescription(), []);
    }

    /** @test */
    public function check_correct_code_from_transfers24()
    {
        $this->refreshHandler();
        $this->http_answer['body'] = 'error=123';
        $this->http_response->addBody($this->http_answer['body']);
        $this->handler->convertResponse();
        $this->assertSame($this->handler->getCode(), '123');
    }

    /** @test */
    public function check_correct_token_from_transfers24()
    {
        $this->refreshHandler();
        $this->http_answer['body'] = 'token=123456789';
        $this->http_response->addBody($this->http_answer['body']);
        $this->handler->convertResponse();
        $this->assertSame($this->handler->getToken(), '123456789');
    }

    /** @test */
    public function check_correct_decode_error_message_from_transfers24()
    {
        $this->refreshHandler();
        $error_array = [
            'p24_url_return' => 'Incorrect_URL',
            'p24_url_status' => 'Incorrect_URL',
        ];
        $this->http_answer['body'] = 'errorMessage=p24_url_return:Incorrect_URL&p24_url_status:Incorrect_URL';
        $this->http_response->addBody($this->http_answer['body']);
        $this->handler->convertResponse();
        $this->assertEquals($this->handler->getErrorDescription(), $error_array);
    }

    protected function refreshHandler()
    {
        $reflection = new \ReflectionClass(HandlerTransfers24::class);
        $handler_property = $reflection->getProperty('http_response');
        $handler_property->setAccessible(true);
        $handler_property->setValue($this->handler, $this->http_response);
    }

    /** @test */
    public function test_verify_payment()
    {
        $this->http_answer = [
            'status' => 200,
            'body' => 'error=4&errorMessage=field1:desc1&field1:desc2',
            'params' => [
                'p24_merchant_id' => '12345678922',
                'p24_pos_id' => '12345678911',
                'p24_session_id' => '12345678933',
                'p24_amount' => '123',
                'p24_currency' => 'PLN',
                'p24_order_id' => '1234567890',
                'p24_sign' => '12345678955',
            ],
        ];
        $this->http_response->addFormParams($this->http_answer['params']);
        $this->http_response->addStatusCode($this->http_answer['status']);
        $this->http_response->addBody($this->http_answer['body']);

        $this->gateway->shouldReceive('checkSum')->andReturn(true);
        $this->gateway->shouldReceive('trnVerify')->andReturn($this->http_response);
        $this->response = $this->handler->receive($this->http_answer['params']);

        $this->assertEquals($this->response->getRequestParameters(), $this->http_response->getFormParams());
        $this->assertEquals($this->handler->getSessionId(), '12345678933');
        $this->assertEquals($this->handler->getOrderId(), '1234567890');
    }
}
