<?php

declare(strict_types=1);

use Koriym\Attributes\AttributeReader;
use Ray\ServiceLocator\ServiceLocator;

require dirname(__DIR__) . '/vendor/autoload.php';

$rm = static function ($dir) use (&$rm) {
    foreach ((array) glob($dir . '/*') as $file) {
        $f = (string) $file;
        is_dir($f) ? $rm($f) : @unlink($f);
        @rmdir($f);
    }
};

$rm(__DIR__ . '/tmp');

// Suppress E_DEPRECATED in vendor files
if (PHP_VERSION_ID >= 80100) {
    set_error_handler(static function (int $errno, string $errstr, string $errfile) {
        return $errno === E_DEPRECATED && str_contains($errfile, dirname(__DIR__) . '/vendor');
    });
}

// no annotation in PHP 8
if (PHP_MAJOR_VERSION >= 8) {
    ServiceLocator::setReader(new AttributeReader());
}
