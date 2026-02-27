<?php

declare(strict_types=1);

namespace Ray\Query;

use Override;

interface RowListInterface extends InvokeInterface
{
    /**
     * @param array<string, mixed> ...$query
     *
     * @return iterable<mixed>
     */
    #[Override]
    public function __invoke(array ...$query): iterable;
}
