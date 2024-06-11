<?php

declare(strict_types=1);

namespace Ray\Query;

use SplFileInfo;

use function file_get_contents;
use function sprintf;

final class DevGetSql implements GetSqlInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(string $filePath): string
    {
        $fileInfo = new SplFileInfo($filePath);

        return sprintf('/* %s */ %s', $fileInfo->getFilename(), (string) file_get_contents($filePath));
    }
}
