<?php

//@codeCoverageIgnoreStart
chdir(__DIR__ . '/../..');

spl_autoload_register(function ($class) {
    $prefix = 'VinaiKopp\\PostCodeFilter\\';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $classFile = str_replace('\\', '/', $class) . '.php';
    foreach (['/../../src/lib/'] as $dir) {
        $file = __DIR__ . $dir . $classFile;
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
    $classFile = str_replace('\\', '/', substr($class, strlen('VinaiKopp\\PostCodeFilter\\'))) . '.php';
    $file = __DIR__ . '/utils/' . $classFile;
    if (file_exists($file)) {
        require $file;
        return;
    }
});

//@codeCoverageIgnoreEnd
