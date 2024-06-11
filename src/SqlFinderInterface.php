<?php

declare(strict_types=1);

namespace Ray\Query;

use ReflectionParameter;

interface SqlFinderInterface
{
    public function __invoke(ReflectionParameter $param): string;
}
