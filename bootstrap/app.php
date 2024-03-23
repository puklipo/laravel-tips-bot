<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
    )->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
