<?php

declare(strict_types=1);

namespace Ray\Query\Annotation;

/**
 * Annotates your class methods into which the Injector should inject values
 *
 * @Annotation
 * @Target("METHOD")
 */
final class AliasQuery
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
    public $templated = false;

    /**
     * @Enum({"row", "row_list"})
     *
     * @var string
     */
    public $type = 'row_list';
}
