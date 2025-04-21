<?php

declare(strict_types=1);

namespace MostlyStatic;

use Illuminate\Contracts\Http\Kernel;
use RuntimeException;

final class RouteMixin
{
    public function static()
    {
        return function ($parameterProvider = null) {
            /** @var Route $this */
            $container = $this->container;
            $app = $container->get(Kernel::class)->getApplication();

            if (! $app->runningInConsole()) {
                return;
            }

            if (! in_array('GET', $this->methods, true)) {
                throw new RuntimeException('Static site generation only works for "GET" routes');
            }

            $parameterProvider ??= DefaultParameterProvider::class;

            $container->get(Registry::class)->register($this, $parameterProvider);
        };
    }
}
