<?php

declare(strict_types=1);

namespace Ray\Query;

use Koriym\ParamReader\ParamReader;
use Koriym\ParamReader\ParamReaderInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class SqlQueryProviderModule extends AbstractModule
{
    public function __construct(?AbstractModule $module = null)
    {
        parent::__construct($module);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bind(SqlFinder::class)->in(Scope::SINGLETON);
        $this->bind(ParamReaderInterface::class)->to(ParamReader::class)->in(Scope::SINGLETON);
        $this->bind(RowInterface::class)->toProvider(RowInterfaceProvider::class)->in(Scope::class);
        $this->bind(RowListInterface::class)->toProvider(RowListInterfaceProvider::class)->in(Scope::class);
        $this->bind(InvokeInterface::class)->toProvider(RowListInterfaceProvider::class)->in(Scope::class);
    }
}
