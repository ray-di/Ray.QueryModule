<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query;

use Ray\Di\AbstractModule;
use Ray\Query\Annotation\AliasQuery;

class SqlQueryModule extends AbstractModule
{
    /**
     * @var string
     */
    private $sqlDir;

    /**
     * @var string
     */
    private $queryBuilderDir;

    public function __construct(string $sqlDir, string $queryBuilderDir = '', AbstractModule $module = null)
    {
        $this->sqlDir = $sqlDir;
        $this->queryBuilderDir = $queryBuilderDir;
        parent::__construct($module);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        foreach ($this->files($this->sqlDir) as $fileInfo) {
            /* @var \SplFileInfo $fileInfo */
            $fullPath = $fileInfo->getPathname();
            $name = pathinfo($fileInfo->getRealPath())['filename'];
            $sqlId = 'sql-' . $name;
            $this->bind(QueryInterface::class)->annotatedWith($name)->toConstructor(
                SqlQuery::class,
                "sql={$sqlId}"
            );
            $this->bind(ItemInterface::class)->annotatedWith($name)->toConstructor(
                SqlQueryRow::class,
                "sql={$sqlId}"
            );
            $this->bind(ListInterface::class)->annotatedWith($name)->toConstructor(
                SqlQuery::class,
                "sql={$sqlId}"
            );
            $this->bind()->annotatedWith($name)->toConstructor(
                SqlQuery::class,
                "sql={$sqlId}"
            );
            $isItem = preg_match("/\w+_item_/", $name);
            $isItem === 1 ? $this->bindCallableItem($name, $sqlId) : $this->bindCallableList($name, $sqlId);

            $sql = trim(file_get_contents($fullPath));
            $this->bind('')->annotatedWith($sqlId)->toInstance($sql);
        }
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(AliasQuery::class),
            [SqlAliasInterceptor::class]
        );
    }

    protected function bindCallableItem(string $name, string $sqlId)
    {
        $this->bind()->annotatedWith($name)->toConstructor(
            SqlQueryRow::class,
            "sql={$sqlId}"
        );
    }

    protected function bindCallableList(string $name, string $sqlId)
    {
        $this->bind()->annotatedWith($name)->toConstructor(
            SqlQuery::class,
            "sql={$sqlId}"
        );
    }

    private function files($dir) : \RegexIterator
    {
        return
            new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(
                        $dir,
                        \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS
                    ),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+\.sql$/',
                \RecursiveRegexIterator::MATCH
            );
    }
}
