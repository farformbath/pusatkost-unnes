<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;

if (! defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (! defined('LARAVEL_VERSION')) {
    define('LARAVEL_VERSION', $app->version());
}

if (! defined('Larastan\\Larastan\\LARAVEL_VERSION')) {
    define('Larastan\\Larastan\\LARAVEL_VERSION', $app->version());
}
