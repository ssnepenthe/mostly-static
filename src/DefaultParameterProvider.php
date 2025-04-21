<?php

declare(strict_types=1);

namespace MostlyStatic;

final class DefaultParameterProvider
{
    public function __invoke()
    {
        yield [];
    }
}
