<?php

namespace Framework\Middleware;

use Exception;
use Framework\Actions\NotFoundAction;
use Framework\Router\Route;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * If a route is attached to the request, call the good callback for it
 *
 * Class DispatcherMiddleware
 * @package Framework\Middlewares
 */
class DispatcherMiddleware
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DispatcherMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $request->getAttribute(Route::class);

        if (is_null($route)) {
            if (class_exists(NotFoundAction::class)) {
                $callback = $this->container->get(NotFoundAction::class);
            } else {
                return $next($request);
            }
        } else {
            $callback = $route->getCallback();
            if (is_string($callback)) {
                $callback = $this->container->get($callback);
            }
        }
        $response = call_user_func_array($callback, [$request]);
        if (is_string($response)) {
            return new Response(200, [], $response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new Exception("The response is not a string or an instance of ResponceInterface");
        }
    }
}
