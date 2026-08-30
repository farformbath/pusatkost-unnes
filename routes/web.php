<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/', function (): Response {
    $appName = config('app.name');

    return Inertia::render('Welcome', [
        'appName' => is_string($appName) ? $appName : 'Pusat Kost UNNES',
        'phpVersion' => PHP_VERSION,
        'laravelVersion' => app()->version(),
    ]);
});
