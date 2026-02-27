<?php

declare(strict_types=1);

namespace Ray\Query\Annotation;

use Attribute;
use Ray\Query\Exception\QueryTypeException;

#[Attribute(Attribute::TARGET_METHOD)]
final class Query
{
    /**
     * Query ID
     *
     * @var string
     */
    public $id;

    /**
     * Is ID templated ?
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
