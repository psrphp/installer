<?php

declare(strict_types=1);

namespace App\Psrphp\Installer\Psrphp;

use App\Psrphp\Installer\Middleware\JumpInstaller;
use PsrPHP\Framework\Framework;
use Psr\EventDispatcher\ListenerProviderInterface;
use PsrPHP\Framework\Handler;
use PsrPHP\Framework\Route;

class ListenerProvider implements ListenerProviderInterface
{
    public function getListenersForEvent(object $event): iterable
    {
        if (is_a($event, Route::class)) {
            yield function () use ($event) {
                Framework::execute(function (
                    Handler $handler,
                    JumpInstaller $jumpInstaller,
                    Route $route
                ) {
                    if (0 !== strpos($route->getHandler(), 'App\\Psrphp\\Installer\\Http\\')) {
                        $handler->unShiftMiddleware($jumpInstaller);
                    }
                }, [
                    Route::class => $event,
                ]);
            };
        }
    }
}
