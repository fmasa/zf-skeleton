<?php

declare(strict_types=1);

namespace Fmasa;

use Nette\DI\Container;
use Throwable;
use Fmasa\Middleware\RoutesToHandler;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\HttpHandlerRunner\RequestHandlerRunner;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\ErrorResponseGenerator;
use Zend\Stratigility\MiddlewarePipe;

final class RequestHandlerRunnerFactory
{
    public static function create(Container $container) : RequestHandlerRunner
    {
        $app = new MiddlewarePipe();

        if (! $container->getParameters()['debugMode']) {
            $app->pipe(
                new ErrorHandler(
                    function () : Response {
                        return new Response();
                    }
                )
            );
        }

        $app->pipe($container->getByType(RoutesToHandler::class));

        return new RequestHandlerRunner(
            $app,
            new SapiEmitter(),
            [ServerRequestFactory::class, 'fromGlobals'],
            function (Throwable $e) {
                $generator = new ErrorResponseGenerator();
                return $generator($e, new ServerRequest(), new Response());
            }
        );
    }
}
