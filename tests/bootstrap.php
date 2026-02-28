<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$rm = static function ($dir) use (&$rm) {
    foreach ((array) glob($dir . '/*') as $file) {
        $f = (string) $file;
        is_dir($f) ? $rm($f) : @unlink($f);
        @rmdir($f);
    }
};

$rm(__DIR__ . '/tmp');

set_error_handler(static function (int $errno, string $errstr, string $errfile) {
    $normalizedFile = str_replace('\\', '/', $errfile);
    $vendorPath = str_replace('\\', '/', dirname(__DIR__) . '/vendor/');

    return $errno === E_DEPRECATED && str_contains($normalizedFile, $vendorPath);
});
