<?php

namespace Tests\Responses\Http;

use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Responses\Http\Response;
use Tests\UnitTestCase;

class ResponseTest extends UnitTestCase
{
    /**
     * @var Response
     */
    private $response;

    protected function setUp()
    {
        parent::setUp();

        $this->response = new Response();
    }

    /** @test */
    public function is_status_code_add_and_get_same()
    {
        $status_code = 200;

        $this->response->addStatusCode($status_code);

        $this->assertEquals($status_code, $this->response->getStatusCode());
    }

    /** @test */
    public function is_body_add_and_get_same()
    {
        $body = 'body of response';

        $this->response->addBody($body);

        $this->assertEquals($body, $this->response->getBody());
    }

    /** @test */
    public function is_form_params_add_and_get_same()
    {
        $form = new RegisterForm();
        $form->addValue('a', 'a');
        $form->addValue('b', 'b');

        $this->response->addFormParams($form);

        $this->assertEquals($form, $this->response->getForm());
    }
}
