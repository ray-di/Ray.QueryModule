<?php

declare(strict_types=1);

namespace Ray\Query\Annotation;

use Attribute;

/**
 * @deprecated use Query instead
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class AliasQuery
{
    /** @var string */
    public $id;

    /** @var bool */
    public $templated = false;

    /** @var 'row'|'row_list' */
    public $type = 'row_list';

    public function __construct(string $id = '', string $type = 'row_list', bool $templated = false)
    {
        $this->id = $id;
        $this->type = $type;
        $this->templated = $templated;
    }
}
