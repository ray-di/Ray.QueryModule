<?php

declare(strict_types=1);

namespace Ray\Query;

use SplFileInfo;

use function sprintf;

final class FileGetContentsWithFileName implements FileGetContentsInterface
{
    /** @var FileGetContents */
    private $getContents;

    public function __construct(FileGetContents $getContents)
    {
        $this->getContents = $getContents;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(string $filePath): string
    {
        $fileInfo = new SplFileInfo($filePath);
        $content = ($this->getContents)($filePath);

        return sprintf('/* %s */ %s', $fileInfo->getFilename(), $content);
    }
}
