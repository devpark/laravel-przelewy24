<?php

namespace Tests;

use Mockery as m;
use PHPUnit_Framework_TestCase;

abstract class UnitTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    protected function setUp()
    {
        parent::setUp();
        $this->app = app();
    }

    public function tearDown()
    {
        m::close();
    }
}
