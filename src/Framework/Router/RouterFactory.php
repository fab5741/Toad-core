<?php

namespace Framework\router;

use Framework\Router;
use Psr\Container\ContainerInterface;

/**
 * Passed to Container Dependency - Describe how to construct the Router
 *
 * Class RouterFactory
 */
class RouterFactory
{
    public function __invoke(ContainerInterface $container): Router
    {
        $cache = null;
        if ($container->get('ENV') === 'production') {
            $cache = 'tmp/routes';
        }
        return new Router($cache);
    }
}
