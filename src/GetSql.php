<?php

declare(strict_types=1);

namespace Ray\Query;

use function file_get_contents;

final class GetSql implements GetSqlInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(string $filePath): string
    {
        return (string) file_get_contents($filePath);
    }
}
