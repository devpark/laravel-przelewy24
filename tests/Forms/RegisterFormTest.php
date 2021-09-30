<?php

namespace Tests\Forms;

use Devpark\Transfers24\Forms\RegisterForm;
use Tests\UnitTestCase;

class RegisterFormTest extends UnitTestCase
{
    /**
     * @var RegisterForm
     */
    private $form;

    protected function setUp()
    {
        parent::setUp();

        $this->form = new RegisterForm();
    }

    /**
     * @Feature Payments
     * @Scenario Register Form
     * @Case get method
     * @test
     */
    public function get_method()
    {
        //When
        $method = $this->form->getMethod();

        //Then
        $this->assertSame('POST', $method);
    }

    /**
     * @Feature Payments
     * @Scenario Register Form
     * @Case get uri
     * @test
     */
    public function get_uri()
    {
        //When
        $method = $this->form->getUri();

        //Then
        $this->assertSame('transaction/register', $method);
    }
}
