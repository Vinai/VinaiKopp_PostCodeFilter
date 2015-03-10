<?php

//@codeCoverageIgnoreStart
chdir(__DIR__ . '/../..');

//require_once __DIR__ . '/../../vendor/autoload.php';

// Closure autoloader from
// https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md#closure-example

spl_autoload_register(function ($class) {
    $prefix = 'VinaiKopp\\PostCodeFilter\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $classFile = str_replace('\\', '/', $class) . '.php';
    foreach (['/../../src/lib/', '/suites/lib/'] as $dir) {
        $file = __DIR__ . $dir . $classFile;
        if (file_exists($file)) {
            require $file;
            break;
        }
    }
});
//@codeCoverageIgnoreEnd
