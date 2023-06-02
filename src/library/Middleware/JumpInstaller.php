<?php

declare(strict_types=1);

namespace App\Psrphp\Installer\Middleware;

use PsrPHP\Framework\Framework;
use PsrPHP\Router\Router;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JumpInstaller implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return Framework::execute(function (
            ResponseFactoryInterface $responseFactory,
            Router $router
        ): ResponseInterface {
            $response = $responseFactory->createResponse(302);
            return $response->withHeader('Location', $router->build('/psrphp/installer/index'));
        });
    }
}
