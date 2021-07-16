<?php

declare(strict_types=1);

namespace Ray\Query;

use SplFileInfo;

use function file_get_contents;
use function sprintf;

final class SqlFileName
{
    /**
     * Return sql file name commented SQL
     */
    public function __invoke(SplFileInfo $fileInfo): string
    {
        return sprintf('/* %s */ %s', $fileInfo->getFilename(), (string) file_get_contents($fileInfo->getPathname()));
    }
}
