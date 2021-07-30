<?php

declare(strict_types=1);

namespace Ray\Query;

use FilesystemIterator;
use Ray\Di\AbstractModule;
use Ray\Query\Annotation\AliasQuery;
use Ray\Query\Annotation\Query;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;

use function file_get_contents;
use function pathinfo;

class SqlQueryModule extends AbstractModule
{
    /** @var string */
    private $sqlDir;

    /** @var callable */
    private $getSql;

    public function __construct(string $sqlDir, ?AbstractModule $module = null, ?callable $getSql = null)
    {
        $this->sqlDir = $sqlDir;
        $this->getSql = $getSql ?? static function (SplFileInfo $fileInfo): string {
            return (string) file_get_contents($fileInfo->getPathname());
        };
        parent::__construct($module);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        /** @var SplFileInfo $fileInfo */
        foreach ($this->files($this->sqlDir) as $fileInfo) {
            $name = pathinfo((string) $fileInfo->getRealPath())['filename'];
            $sqlId = 'sql-' . $name;
            $this->bind(QueryInterface::class)->annotatedWith($name)->toConstructor(
                SqlQueryRowList::class,
                "sql={$sqlId}"
            );
            $this->bindCallableItem($name, $sqlId);
            $this->bindCallableList($name, $sqlId);

            $sql = (string) ($this->getSql)($fileInfo);
            $this->bind('')->annotatedWith($sqlId)->toInstance($sql);
        }

        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Query::class),
            [QueryInterceptor::class]
        );
        // <=0.4.0
        /** @psalm-suppress DeprecatedClass */
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(AliasQuery::class),
            [SqlAliasInterceptor::class]
        );
    }

    protected function bindCallableItem(string $name, string $sqlId): void
    {
        $this->bind(RowInterface::class)->annotatedWith($name)->toConstructor(
            SqlQueryRow::class,
            "sql={$sqlId}"
        );
    }

    protected function bindCallableList(string $name, string $sqlId): void
    {
        $this->bind()->annotatedWith($name)->toConstructor(
            SqlQueryRowList::class,
            "sql={$sqlId}"
        );
        $this->bind(RowListInterface::class)->annotatedWith($name)->toConstructor(
            SqlQueryRowList::class,
            "sql={$sqlId}"
        );
    }

    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function files(string $dir): RegexIterator
    {
        return new RegexIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $dir,
                    FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::LEAVES_ONLY
            ),
            '/^.+\.sql$/',
            RecursiveRegexIterator::MATCH
        );
    }
}
