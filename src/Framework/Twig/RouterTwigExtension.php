<?php

namespace Framework\Twig;

use Framework\Router;

class RouterTwigExtension extends \Twig_Extension
{

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path', [$this, 'pathFor']),
            new \Twig_SimpleFunction('is_subPath', [$this, 'is_subPath'])
        ];
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function is_subPath(string $path): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($path);
        return strpos($uri, $expectedUri) !== false;
    }
}
