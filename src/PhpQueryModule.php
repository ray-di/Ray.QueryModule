<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Di\AbstractModule;

class PhpQueryModule extends AbstractModule
{
    private $configs;

    public function __construct(iterable $configs, AbstractModule $module = null)
    {
        $this->configs = $configs;
        parent::__construct($module);
    }

    protected function configure()
    {
        foreach ($this->configs as $name => $binding) {
            $this->bindQuery($name, $binding);
            $this->bind()->annotatedWith($name)->to($binding);
        }
    }

    private function bindQuery(string $name, $binding)
    {
        if (is_string($binding) && class_exists($binding) && (new \ReflectionClass($binding))->implementsInterface(QueryInterface::class)) {
            $this->bind(QueryInterface::class)->annotatedWith($name)->to($binding);
        }
    }
}
