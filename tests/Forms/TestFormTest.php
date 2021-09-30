<?php

namespace Tests\Forms;

use Devpark\Transfers24\Forms\TestForm;
use Tests\UnitTestCase;

class TestFormTest extends UnitTestCase
{
    /**
     * @var TestForm
     */
    private $form;

    protected function setUp()
    {
        parent::setUp();

        $this->form = new TestForm();
    }

    /**
     * @Feature Payments
     * @Scenario Test Form
     * @Case get method
     * @test
     */
    public function get_method()
    {
        //When
        $method = $this->form->getMethod();

        //Then
        $this->assertSame('GET', $method);
    }

    /**
     * @Feature Payments
     * @Scenario Test Form
     * @Case get uri
     * @test
     */
    public function get_uri()
    {
        //When
        $method = $this->form->getUri();

        //Then
        $this->assertSame('testAccess', $method);
    }
}
