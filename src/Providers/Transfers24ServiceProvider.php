<?php

namespace Devpark\Transfers24\Providers;

use Illuminate\Support\ServiceProvider;

class Transfers24ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // merge module config if it's not published or some entries are missing
        $this->mergeConfigFrom($this->configFile(), 'transfers24');
        // publish configuration file
        $this->publishes([
            $this->configFile() => $this->app['path.config'] . DIRECTORY_SEPARATOR . 'transfers24.php',
        ], 'config');
    }

    /**
     * Get module config file.
     *
     * @return string
     */
    protected function configFile()
    {
        return realpath(__DIR__ . '/../../config/transfers24.php');
    }
}
