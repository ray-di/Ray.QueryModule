<?php

declare(strict_types=1);

namespace Ray\Query;

interface FileGetContentsInterface
{
    public function __invoke(string $filePath): string;
}
