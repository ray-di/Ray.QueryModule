<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Override;
use Ray\Di\InjectionPointInterface;
use Ray\Di\ProviderInterface;

/**
 * @psalm-api
 * @implements ProviderInterface<RowInterface>
 */
final class RowInterfaceProvider implements ProviderInterface
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

    #[Override]
    public function get(): SqlQueryRow
    {
        return new SqlQueryRow($this->pdo, ($this->finder)($this->ip->getParameter()));
    }
}
