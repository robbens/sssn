<?php

namespace Robbens\Sssn;

use Illuminate\Support\ServiceProvider;

class SssnServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Sssn::class, function() {
            return new Sssn();
        });
    }
}
