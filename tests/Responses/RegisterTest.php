<?php

namespace Tests\Responses;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Services\DecodedBody;
use Tests\UnitTestCase;
use Devpark\Transfers24\Responses\Register as ResponseRegister;
use Mockery as m;

class RegisterTest extends UnitTestCase
{
    /**
     * @var m\MockInterface
     */
    private $decoded_body;
    /**
     * @var m\MockInterface
     */
    private $form;
    /**
     * @var ResponseRegister
     */
    private $response;

    protected function setUp()
    {
        parent::setUp();

        $this->form = m::mock(Form::class);
        $this->decoded_body = m::mock(DecodedBody::class);

        $this->response = new ResponseRegister($this->form, $this->decoded_body);
    }

    /** @test */
    public function verified_return_token()
    {
        $token_handler = '123456789';
        $this->decoded_body->shouldReceive('getToken')->andReturn($token_handler);

        $token = $this->response->getToken();
        $this->assertEquals($token, $token_handler);
    }

    /** @test */
    public function check_response_success()
    {
        $this->decoded_body->shouldReceive('getStatusCode')->once()->andReturn('0');

        $success = $this->response->isSuccess();
        $this->assertTrue($success);
    }

    /** @test */
    public function check_correct_error_code_passing()
    {
        $code = 4;
        $this->decoded_body->shouldReceive('getStatusCode')->once()->andReturn($code);

        $error_code = $this->response->getErrorCode();
        $this->assertEquals($error_code, $code);
    }

    /** @test */
    public function check_correct_error_description_passing()
    {
        $error_description = 'error 1 desc';

        $this->decoded_body->shouldReceive('getErrorMessage')->once()->andReturn($error_description);

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
        $this->form->shouldReceive('toArray')->once()->andReturn($parameters_request);

        $parameters = $this->response->getRequestParameters();
        $this->assertEquals($parameters, $parameters_request);
    }

    /** @test */
    public function check_correct_session_id_passing()
    {
        $session_id = 4;
        $this->form->shouldReceive('getSessionId')->once()->andReturn($session_id);

        $passing_session_id = $this->response->getSessionId();
        $this->assertEquals($passing_session_id, $session_id);
    }
}
