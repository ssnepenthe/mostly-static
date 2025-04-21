<?php

declare(strict_types=1);

namespace MostlyStatic;

use Illuminate\Routing\Route;
use SplObjectStorage;

final class Registry
{
    protected $store;

    public function __construct()
    {
        $this->store = new SplObjectStorage();
    }

    public function register(Route $route, $parameterProvider)
    {
        $this->store[$route] = $parameterProvider;
    }

    public function routes()
    {
        foreach ($this->store as $route) {
            yield $route => $this->store[$route];
        }
    }
}
