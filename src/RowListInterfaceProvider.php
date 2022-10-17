<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\InjectionPointInterface;
use Ray\Di\ProviderInterface;

final class RowListInterfaceProvider implements ProviderInterface
{
    /** @var InjectionPointInterface */
    private $ip;

    /** @var ExtendedPdoInterface */
    private $pdo;

    /** @var SqlFinder */
    private $finder;

    public function __construct(
        InjectionPointInterface $ip,
        ExtendedPdoInterface $pdo,
        SqlFinder $finder
    ) {
        $this->ip = $ip;
        $this->pdo = $pdo;
        $this->finder = $finder;
    }

    public function get(): SqlQueryRowList
    {
        return new SqlQueryRowList($this->pdo, ($this->finder)($this->ip->getParameter()));
    }
}
