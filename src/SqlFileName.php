<?php

declare(strict_types=1);

namespace Ray\Query;

use SplFileInfo;

final class SqlFileName
{
    /**
     * Return sql file name commented SQL
     */
    public function __invoke(SplFileInfo $fileInfo): string
    {
        $getFileContents = new FileGetContentsWithFileName(new FileGetContents());
        $filePath = $fileInfo->getPathname();

        return $getFileContents($filePath);
    }
}
