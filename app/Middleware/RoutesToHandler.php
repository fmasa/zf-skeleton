<?php

declare(strict_types=1);

namespace Fmasa\Middleware;

use FastRoute\Dispatcher;
use Fig\Http\Message\StatusCodeInterface;
use Nette\DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use function assert;

final class RoutesToHandler implements MiddlewareInterface
{
    /** @var Dispatcher */
    private $dispatcher;

    /** @var Container */
    private $container;

    public function __construct(Dispatcher $dispatcher, Container $container)
    {
        $this->dispatcher = $dispatcher;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $nextHandler) : ResponseInterface
    {
        $result = $this->dispatcher->dispatch(
            $request->getMethod(),
            rawurldecode($request->getUri()->getPath())
        );

        if ($result[0] === Dispatcher::NOT_FOUND) {
            return $this->createErrorResponse(StatusCodeInterface::STATUS_NOT_FOUND, 'Endpoint not found');
        }

        if ($result[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            return $this->createErrorResponse(
                StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED,
                sprintf(
                    'Method %s is not allowed on this endpoint. Allowed methods: %s',
                    $request->getMethod(),
                    implode(', ', $result[1])
                )
            );
        }

        $handler = $this->container->getByType($result[1]);

        assert($handler instanceof RequestHandlerInterface);

        return $handler->handle($request);
    }

    private function createErrorResponse(int $code, string $message) : ResponseInterface
    {
        return new JsonResponse(['error' => $message], $code);
    }
}
