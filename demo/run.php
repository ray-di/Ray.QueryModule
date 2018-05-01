<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
passthru('php ' . __DIR__ . '/0-manual-injection.php');
passthru('php ' . __DIR__ . '/1-constructor-injection.php');
passthru('php ' . __DIR__ . '/2-alias-query.php');
