<?php

declare(strict_types=1);
/**
 * This file is part of the Ray.Query.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
$rm = function ($dir) use (&$rm) {
    foreach ((array) glob($dir . '/*') as $file) {
        $f = (string) $file;
        is_dir($f) ? $rm($f) : @unlink($f);
        @rmdir($f);
    }
};
$rm(__DIR__ . '/tmp');
