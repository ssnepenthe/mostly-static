<?php

declare(strict_types=1);

namespace MostlyStatic;

use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;
use MostlyStatic\Commands\Generate;

final class MostlyStaticServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(Generate::class);
        }

        Route::mixin(new RouteMixin());
    }

    public function register()
    {
        $this->app->singleton(Registry::class);
    }
}
