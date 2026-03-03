<?php

declare(strict_types=1);

namespace Ray\Query;

use Ray\Query\Annotation\Sql;
use Ray\Query\Exception\SqlFileNotFoundException;
use Ray\Query\Exception\SqlNotAnnotatedException;
use ReflectionParameter;

use function file_exists;
use function file_get_contents;
use function sprintf;

/** @psalm-api */
final class SqlFinder
{
    public function __construct(
        private readonly SqlDir $sqlDir,
    ) {
    }

    public function __invoke(ReflectionParameter $param): string
    {
        $attrs = $param->getAttributes(Sql::class);
        if ($attrs === []) {
            throw new SqlNotAnnotatedException((string) $param);
        }

        $sqlAnnotation = $attrs[0]->newInstance();

        $file = sprintf('%s/%s.sql', $this->sqlDir->value, $sqlAnnotation->sql);
        if (! file_exists($file)) {
            $msg = sprintf('%s:%s', (string) $param, $file);

            throw new SqlFileNotFoundException($msg);
        }

        return (string) file_get_contents($file);
    }
}
