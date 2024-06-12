<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Exception\SqlFileNotReadableException;

use function file_get_contents;

final class FileGetContents implements FileGetContentsInterface
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(string $filePath): string
    {
        $content =  file_get_contents($filePath);
        if ($content === false) {
            // @codeCoverageIgnoreStart
            throw new SqlFileNotReadableException($filePath);
            // @codeCoverageIgnoreEnd
        }

        return $content;
    }
}
