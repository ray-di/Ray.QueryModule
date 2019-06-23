<?php

declare(strict_types=1);

$rm = function ($dir) use (&$rm) {
    foreach ((array) glob($dir . '/*') as $file) {
        $f = (string) $file;
        is_dir($f) ? $rm($f) : @unlink($f);
        @rmdir($f);
    }
};
$rm(__DIR__ . '/tmp');
