<?php

spl_autoload_register(static function (string $class): void {
    $paths = [
        BASE_PATH . '/src/Core/' . $class . '.php',
        BASE_PATH . '/src/Repositories/' . $class . '.php',
        BASE_PATH . '/src/Services/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});
