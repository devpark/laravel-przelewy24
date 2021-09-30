<?php

namespace Tests;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\RouteCollectionInterface;
use Mockery as m;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;

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
        $this->bindAppContainer();
        $this->skipLogs();
    }

    public function tearDown()
    {
        m::close();
    }

    protected function bindAppContainer(): void
    {
        $this->app->instance(Container::class, $this->app);
        $this->app->instance(\Illuminate\Container\Container::class, $this->app);

        $this->app->bind(RouteCollectionInterface::class, RouteCollection::class);
    }

    protected function skipLogs(): void
    {
        $this->app->bind(LoggerInterface::class, TestLogger::class);
    }
}
