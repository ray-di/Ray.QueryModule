<?php

declare(strict_types=1);

namespace Ray\Query\Annotation;

use Attribute;
use Ray\Query\Exception\QueryTypeException;

/**
 * Runs the query of the ID with the method arguments and returns its result
 * (a resource object gets the result as its body, or 404 when the result is empty)
 *
 * @see \Ray\Query\QueryInterceptor
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Query
{
    /** @var string */
    public $id;

    /**
     * Whether `id` is a URI template expanded with the method arguments
     *
     * @var bool
     */
    public $templated;

    /** @var 'row'|'row_list' */
    public $type = 'row_list';

    /**
     * @psalm-api
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function __construct(string $id, string $type = 'row_list', bool $templated = false)
    {
        $this->id = $id;
        $this->templated = $templated;
        if (! ($type === 'row') && ! ($type === 'row_list')) {
            throw new QueryTypeException($type);
        }

        $this->type = $type;
    }
}
