<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Ray\Query\Annotation;

/**
 * Annotates your class methods into which the Injector should inject values
 *
 * @Annotation
 * @Target("METHOD")
 */
final class AliasSql
{
    /**
     * @var string
     */
    public $sql;

    /**
     * @var string
     */
    public $key;
}
