<?php

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Route request to his content - Build on FastRouteRouter
 * More infos - https://github.com/zendframework/zend-expressive-fastroute
 *
 * Class Router
 */
class Router
{
    /**
     * @var FastRouteRouter
     */
    private $router;

    /**
     * Router constructor.
     * @param null|string $cache
     */
    public function __construct(?string $cache = null)
    {
        $this->router = new FastRouteRouter(null, null, [
            FastRouteRouter::CONFIG_CACHE_ENABLED => !is_null($cache),
            FastRouteRouter::CONFIG_CACHE_FILE => $cache
        ]);
    }

    /**
     * CRUD route generations
     *
     * @param string $prefixPath
     * @param $callable
     * @param string $prefixName
     */
    public function crud(string $prefixPath, $callable, string $prefixName)
    {
        $this->get($prefixPath, $callable, "$prefixName.index");

        $this->get("$prefixPath/new", $callable, "$prefixName.create");
        $this->post("$prefixPath/new", $callable);

        $this->get("$prefixPath/{id:\d+}", $callable, "$prefixName.edit");
        $this->post("$prefixPath/{id:\d+}", $callable);
        $this->delete("$prefixPath/{id:\d+}", $callable, "$prefixName.delete");
    }

    /**
     * Add GET Requests
     *
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function get(string $path, $callable, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ["GET"], $name));
    }

    /**
     * Add POST Requests
     *
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function post(string $path, $callable, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ["POST"], $name));
    }

    /**
     * Add DELETE Requests
     *
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function delete(string $path, $callable, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ["DELETE"], $name));
    }

    /**
     * Find a coressponding route for an incoming Request
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestinterface $request):?Route
    {
        $result = $this->router->match($request);
        if ($result->isSuccess() === true) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        } else {
            return null;
        }
    }

    /**
     * Generate an uri for a route by its name
     *
     * @param string $name
     * @param array $params
     * @param array $queryParams
     * @return null|string
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []):?string
    {
        $uri = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
}
