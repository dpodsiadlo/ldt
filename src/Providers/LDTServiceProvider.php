<?php

namespace DPodsiadlo\LDT\Providers;

use Log;
use DPodsiadlo\LDT\LDT;
use DPodsiadlo\LDT\Handlers\LDTHandler;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

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

        DB::listen(function ($query) {
            \DPodsiadlo\LDT\Facades\LDT::query($query);
        });
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
