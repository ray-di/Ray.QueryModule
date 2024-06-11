<?php

declare(strict_types=1);

namespace Ray\Query;

use Aura\Sql\ExtendedPdoInterface;
use Ray\Di\Exception\Unbound;
use Ray\Di\InjectionPointInterface;
use Ray\Di\InjectorInterface;
use Ray\Di\ProviderInterface;
use Ray\Query\Exception\SqlFileNotFoundException;

use function assert;
use function error_log;
use function sprintf;

/** @implements ProviderInterface<QueryInterface> */
final class RowListInterfaceProvider implements ProviderInterface
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
        } catch (SqlFileNotFoundException $e) {
            try {
                $named = $e->sql;
                // Try "RowListInterface"
                $instance = $this->injector->getInstance(RowListInterface::class, $named);
                // For development
                // @codeCoverageIgnoreStart
                assert($instance instanceof QueryInterface);
                error_log(sprintf('Warning: #[Sql(\'%s\')] is not vald. Change to #[\\Ray\\Di\\Di\\Named(\'%s\')]', $named, $named));

                return $instance;
            } catch (Unbound $unbound) {
                try {
                    assert(isset($named)); // @phpstan-ignore-line
                    // try "callable"
                    /** @var QueryInterface $instance */
                    $instance = $this->injector->getInstance('', $named);
                    assert($instance instanceof QueryInterface);
                    error_log(sprintf('Warning: #[Sql(\'%s\')] is not vald. Change to #[\\Ray\\Di\\Di\\Named(\'%s\')]', $named, $named));

                    return $instance;
                } catch (Unbound $unbound) {
                    throw $e;
                }
            // @codeCoverageIgnoreEnd
            }
        }

        return new SqlQueryRowList($this->pdo, $sql);
    }
}
