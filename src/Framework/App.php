<?php

namespace Framework;

use DI\ContainerBuilder;
use Framework\Exceptions\NoMiddleWareException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App implements DelegateInterface
{
    /**
     * List of modules
     * @var array
     */
    private $modules = [];
    /**
     * @var string
     */
    private $definition;

    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * @var int
     */
    private $index = 0;

    /**
     * App constructor.
     * @param string $definition
     */
    public function __construct(string $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Activate a module - See Module class for more infos
     *
     * @param string $module
     * @return $this
     */
    public function addModule(string $module)
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * @param string $middleware
     * @return $this
     */
    public function pipe(string $middleware)
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Run the app, basically just circle through modules and middlewares
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->process($request);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
//            $env = getenv('ENV') ?: 'production';
//            if ($env === 'production') {
//                // TODO: implements apcucache
//                $builder->setDefinitionCache(new FilesystemCache("/tmp/di"));
//                $builder->writeProxiesToFile(true, 'tmp/proxies');
//            }
            $builder->addDefinitions($this->definition);
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }
            $this->container = $builder->build();
        }
        return $this->container;
    }

    /**
     * Process a middleware to current request and goes to next midlleware
     * until response is returned or no more middleware is found
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws NoMiddleWareException
     */
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new NoMiddleWareException('Aucun midlleware n\' intercepté cette requête');
        } else {
            if (is_callable($middleware)) {
                return call_user_func_array($middleware, [$request, [$this, 'process']]);
            } elseif ($middleware instanceof MiddlewareInterface) {
                return $middleware->process($request, $this);
            }
        }
    }

    /**
     * Return the current middleware
     *
     * @return mixed|null
     */
    private function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->getContainer()->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
