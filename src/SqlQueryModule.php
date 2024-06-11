<?php

declare(strict_types=1);

namespace Ray\Query;

use Koriym\ParamReader\ParamReader;
use Koriym\ParamReader\ParamReaderInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class SqlQueryModule extends AbstractModule
{
    /** @var string  */
    private $sqlDir;

    public function __construct(string $sqlDir, ?AbstractModule $module = null)
    {
        $this->sqlDir = $sqlDir;

        parent::__construct($module);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->bind(SqlDir::class)->toInstance(new SqlDir($this->sqlDir));
        $this->bind(SqlFinderInterface::class)->to(SqlFinder::class)->in(Scope::SINGLETON);
        $this->bind(ParamReaderInterface::class)->to(ParamReader::class)->in(Scope::SINGLETON);
        $this->bind(RowInterface::class)->toProvider(RowInterfaceProvider::class);
        $this->bind(RowListInterface::class)->toProvider(RowListInterfaceProvider::class);
        $this->bind(InvokeInterface::class)->toProvider(RowListInterfaceProvider::class);
        $this->bind(QueryInterface::class)->toProvider(RowListInterfaceProvider::class);
        // AOP
        $this->install(new SqlQueryInterceptModule());
    }
}
