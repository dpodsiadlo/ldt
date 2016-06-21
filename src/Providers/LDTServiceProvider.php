<?php

namespace DPodsiadlo\Providers;

use Log;
use DPodsiadlo\LDT;
use DPodsiadlo\Handlers\LDTHandler;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

class LDTServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $monolog = Log::getMonolog();
        $monolog->pushHandler(new LDTHandler());


    }

    public function register()
    {
        $this->app->singleton(LDT::class, function () {
            return new LDT();
        });

    }

    public function provides()
    {
        return [LDT::class];
    }

}
