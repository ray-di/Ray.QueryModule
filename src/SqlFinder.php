<?php

declare(strict_types=1);

namespace Ray\Query;

use Koriym\ParamReader\ParamReaderInterface;
use Ray\Query\Annotation\Sql;
use Ray\Query\Exception\SqlFileNotFoundException;
use Ray\Query\Exception\SqlNotAnnotatedException;
use ReflectionParameter;

use function file_exists;
use function sprintf;

final class SqlFinder implements SqlFinderInterface
{
    /** @var ParamReaderInterface<object> */
    private $reader;

    /** @var SqlDir */
    private $sqlDir;

    /** @var FileGetContentsInterface */
    private $getSql;

    /** @param ParamReaderInterface<object> $reader */
    public function __construct(
        ParamReaderInterface $reader,
        SqlDir $sqlDir,
        FileGetContentsInterface $getSql
    ) {
        $this->reader = $reader;
        $this->sqlDir = $sqlDir;
        $this->getSql = $getSql;
    }

    public function __invoke(ReflectionParameter $param): string
    {
        /** @var ?Sql $sqlAnnotation */
        $sqlAnnotation = $this->reader->getParametrAnnotation($param, Sql::class);
        if ($sqlAnnotation === null) {
            throw new SqlNotAnnotatedException((string) $param);
        }

        $file = sprintf('%s/%s.sql', $this->sqlDir->value, $sqlAnnotation->sql);
        if (! file_exists($file)) {
            $msg = sprintf('%s:%s', (string) $param, $file);

            throw new SqlFileNotFoundException($msg, $sqlAnnotation->sql);
        }

        return ($this->getSql)($file);
    }
}
