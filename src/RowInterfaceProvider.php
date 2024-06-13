<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\Exception\Unbound;
use Ray\Di\InjectionPointInterface;
use Ray\Di\InjectorInterface;
use Ray\Di\ProviderInterface;
use Ray\Query\Exception\SqlFileNotFoundException;

use function error_log;
use function sprintf;

/** @implements ProviderInterface<QueryInterface> */
final class RowInterfaceProvider implements ProviderInterface
{
    /** @var InjectionPointInterface */
    private $ip;

    /** @var ExtendedPdoInterface */
    private $pdo;

    /** @var SqlFinderInterface */
    private $finder;

    /** @var InjectorInterface  */
    private $injector;

    public function __construct(
        InjectionPointInterface $ip,
        ExtendedPdoInterface $pdo,
        SqlFinderInterface $finder,
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
            // For development
            // @codeCoverageIgnoreStart
        } catch (SqlFileNotFoundException $e) {
            return $this->handleSqlNotFound($e);
        }

        return new SqlQueryRow($this->pdo, $sql);
    }

    private function handleSqlNotFound(SqlFileNotFoundException $e): RowInterface
    {
        try {
            $named = $e->sql;
            $instance = $this->injector->getInstance(RowInterface::class, $named);
            error_log(sprintf('Warning: #[Sql(\'%s\')] is not vald. Change to #[\\Ray\\Di\\Di\\Named(\'%s\')]', $named, $named));

            return $instance;
        } catch (Unbound $unbound) {
            throw $e;
        }
        // @codeCoverageIgnoreEnd
    }
}
