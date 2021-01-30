<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdo;
use PHPUnit\Framework\TestCase;

use Ray\Query\Annotation\Query;
use Ray\Query\Exception\QueryTypeException;
use function file_get_contents;

class AnnotationTest extends TestCase
{
    public function testInvoke(): void
    {
        $this->expectException(QueryTypeException::class);
        new Query('', '__invalid__');
    }
}
