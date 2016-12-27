<?php

namespace Tests\Responses\Http;

use Tests\UnitTestCase;
use Devpark\Transfers24\Responses\Http\Response;

class ResponseTest extends UnitTestCase
{
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
        $form_params = [
            'a' => 'a',
            'b' => 'b',
        ];

        $this->response->addFormParams($form_params);

        $this->assertEquals(count($form_params), count($this->response->getFormParams()));
        $this->assertEquals($form_params, $this->response->getFormParams());
    }
}
