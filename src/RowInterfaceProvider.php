<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\Exception\Unbound;
use Ray\Di\InjectionPointInterface;
use Ray\Di\InjectorInterface;
use Ray\Di\ProviderInterface;
use Ray\Query\Exception\SqlFileNotFoundException;
use Throwable;

/** @implements ProviderInterface<RowInterface> */
final class RowInterfaceProvider implements ProviderInterface
{
    /** @var InjectionPointInterface */
    private $ip;

    /** @var ExtendedPdoInterface */
    private $pdo;

    /** @var SqlFinder */
    private $finder;
    private $injector;

    public function __construct(
        InjectionPointInterface $ip,
        ExtendedPdoInterface $pdo,
        SqlFinder $finder,
        InjectorInterface $injector
    ) {
        $this->ip = $ip;
        $this->pdo = $pdo;
        $this->finder = $finder;
        $this->injector = $injector;
    }

    public function get(): SqlQueryRow
    {
        try {
            $sql = ($this->finder)($this->ip->getParameter());
        } catch (SqlFileNotFoundException | Throwable $e) {
            try {
                $named = $e->sql;

                return $this->injector->getInstance(RowInterface::class, $named);
            } catch (Unbound $unbound) {
                throw $e;
            }
        }

        return new SqlQueryRow($this->pdo, $sql);
    }
}
