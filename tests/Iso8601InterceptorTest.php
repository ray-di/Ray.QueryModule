<?php

namespace Ray\Query;

use function date;
use PHPUnit\Framework\TestCase;
use Ray\Aop\ReflectiveMethodInvocation;

class Iso8601InterceptorTest extends TestCase
{

    public function testDateTimeFieldConvertedIso8601()
    {
        $object = new class {
            public function run()
            {
                return [
                    ['created' => '1970-01-01 00:00:00']
                ];
            }
        };
        $interceptor = new Iso8601Interceptor(['created']);
        $invocation = new ReflectiveMethodInvocation($object, 'run', [], [$interceptor]);
        $result = $invocation->proceed();
        $this->assertSame('1970-01-01T00:00:00+01:00', $result[0]['created']);
    }

    public function testRowInterfaceConvert()
    {
        $object = new class implements RowInterface {

            public function __invoke(array ...$query) : iterable
            {
                return ['created' => '1970-01-01 00:00:00'];
            }

            public function run()
            {
            }
        };
        $interceptor = new Iso8601Interceptor(['created']);
        $invocation = new ReflectiveMethodInvocation($object, '__invoke', [], [$interceptor]);
        $result = $invocation->proceed();
        $this->assertSame('1970-01-01T00:00:00+01:00', $result['created']);
    }
}
