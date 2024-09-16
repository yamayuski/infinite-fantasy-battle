<?php

declare(strict_types=1);

/**
 * @license MIT
 */

require_once __DIR__ . '/vendor/autoload.php';

use Ifb\Infrastructure\Config\ArrayConfig;
use Ifb\Kernel;

(static function (): void {
    $array = require_once __DIR__ . '/config.php';
    if (!\is_array($array)) {
        throw new LogicException('Invalid config.php');
    }
    $config = new ArrayConfig($array);
    $kernel = new Kernel($config);
    $kernel->boot();
    $kernel->serveRoadRunner();
})();
