<?php

namespace Tests\Providers;

use Devpark\Transfers24\Providers\Transfers24ServiceProvider;
use Illuminate\Foundation\Application;
use Tests\UnitTestCase;
use Mockery as m;

class Transfers24ServiceProviderTest extends UnitTestCase
{
    /** @test */
    public function it_does_all_required_actions_when_registering()
    {
        $app = m::mock(Application::class);
        $moduleConfigFile = realpath(__DIR__ . '/../../config/transfers24.php');
        $configPath = 'dummy/config/path';
        $transfers24_provider = m::mock(Transfers24ServiceProvider::class, [$app])->makePartial()->shouldAllowMockingProtectedMethods();

        // merge config
        $transfers24_provider->shouldReceive('mergeConfigFrom')
            ->with($moduleConfigFile, 'transfers24')->once();

        // publishing configuration files
        $app->shouldReceive('offsetGet')->with('path.config')->once()->andReturn($configPath);
        $transfers24_provider->shouldReceive('publishes')->once()->with([
            $moduleConfigFile => $configPath . DIRECTORY_SEPARATOR . 'transfers24.php',
        ], 'config');

        $transfers24_provider->register();
    }
}
