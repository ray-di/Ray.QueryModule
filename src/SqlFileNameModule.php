<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class SqlFileNameModule extends AbstractModule
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->bind(FileGetContentsInterface::class)->to(FileGetContentsWithFileName::class)->in(Scope::SINGLETON);
    }
}
