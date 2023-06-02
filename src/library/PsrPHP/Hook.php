<?php

declare(strict_types=1);

namespace App\Psrphp\Installer;

use App\Psrphp\Installer\Middleware\JumpInstaller;
use PsrPHP\Framework\Route;
use PsrPHP\Psr15\RequestHandler;

class App
{
    public static function onStart(
        RequestHandler $requestHandler,
        JumpInstaller $jumpInstaller,
        Route $route
    ) {
        if ($route->getApp() != 'psrphp/installer') {
            $requestHandler->appendMiddleware($jumpInstaller);
        }
    }
}
