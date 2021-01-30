<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Di\AbstractModule;
use ReflectionClass;

use function class_exists;
use function is_string;

class PhpQueryModule extends AbstractModule
{
    /** @var iterable<string, mixed> */
    private $configs;

    /**
     * @param iterable<string, mixed> $configs
     */
    public function __construct(iterable $configs, ?AbstractModule $module = null)
    {
        $this->configs = $configs;
        parent::__construct($module);
    }

    protected function configure(): void
    {
        /** @var string $binding */
        foreach ($this->configs as $name => $binding) {
            $this->bindQuery($name, $binding);
            $this->bind()->annotatedWith($name)->to($binding);
        }
    }

    /**
     * @param mixed $binding
     */
    private function bindQuery(string $name, $binding): void
    {
        if (is_string($binding) && class_exists($binding) && (new ReflectionClass($binding))->implementsInterface(QueryInterface::class)) {
            $this->bind(QueryInterface::class)->annotatedWith($name)->to($binding);
        }
    }
}
