<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\Exception\Unbound;
use Ray\Di\InjectionPointInterface;
use Ray\Di\InjectorInterface;
use Ray\Di\ProviderInterface;
use Ray\Query\Exception\SqlFileNotFoundException;

/** @implements ProviderInterface<QueryInterface> */
final class RowListInterfaceProvider implements ProviderInterface
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

    public function get(): QueryInterface
    {
        try {
            $sql = ($this->finder)($this->ip->getParameter());
        } catch (SqlFileNotFoundException $e) {
            try {
                $named = $e->sql;

                return $this->injector->getInstance(RowListInterface::class, $named);
            } catch (Unbound $unbound) {
                try {
                    return $this->injector->getInstance('', $named);
                } catch (Unbound $unbound) {
                    throw $e;
                }
            }
        }

        return new SqlQueryRowList($this->pdo, $sql);
    }
}
