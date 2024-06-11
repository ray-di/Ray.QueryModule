<?php

declare(strict_types=1);

namespace Ray\Query;

interface GetSqlInterface
{
    public function __invoke(string $filePath): string;
}
