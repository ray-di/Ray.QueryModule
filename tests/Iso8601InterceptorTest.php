<?php

declare(strict_types=1);

namespace Ray\Query;

use PHPUnit\Framework\TestCase;
use Ray\Aop\ReflectiveMethodInvocation;

class Iso8601InterceptorTest extends TestCase
{
    public function testDateTimeFieldConvertedIso8601() : void
    {
        $object = new class {
            /**
             * @return array<array<string>>
             */
            public function run() : array
            {
                return [
                    ['created' => '1970-01-01 00:00:00']
                ];
            }
        };
        $interceptor = new Iso8601Interceptor(['created']);
        $invocation = new ReflectiveMethodInvocation($object, 'run', [], [$interceptor]);
        $result = $invocation->proceed();
        $this->assertSame('1970-01-01T00:00:00+00:00', $result[0]['created']);
    }

    public function testRowInterfaceConvert() : void
    {
        $object = new class implements RowInterface {
            public function __invoke(array ...$query) : iterable
            {
                return ['created' => '1970-01-01 00:00:00'];
            }

            public function run() : void
            {
            }
        };
        $interceptor = new Iso8601Interceptor(['created']);
        $invocation = new ReflectiveMethodInvocation($object, '__invoke', [], [$interceptor]);
        $result = $invocation->proceed();
        $this->assertSame('1970-01-01T00:00:00+00:00', $result['created']);
    }
}
