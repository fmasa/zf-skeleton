<?php

declare(strict_types=1);

namespace Fmasa;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Fmasa\Handler\HelloWorldHandler;
use function FastRoute\simpleDispatcher;

final class DispatcherFactory
{
    public function __invoke() : Dispatcher
    {
        return simpleDispatcher(function (RouteCollector $routes) {
            $routes->addRoute('GET', '/', HelloWorldHandler::class);
        });
    }
}
