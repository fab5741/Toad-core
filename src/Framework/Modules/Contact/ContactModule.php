<?php

namespace Framework\Modules\Contact;

use Framework\Module;
use Framework\Modules\Contact\Actions\IndexAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class ContactModule extends Module
{

    const DEFINITIONS = __DIR__ . "/config.php";
    /**
     * @var RendererInterface
     */
    private $renderer;
    /**
     * @var contact.prefix
     */
    private $prefix;

    public function __construct(ContainerInterface $container, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('contact', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $this->prefix = $container->get('contact.prefix');
        $router->get($this->prefix, IndexAction::class, 'contact.index');
        $router->post($this->prefix, IndexAction::class);
    }

    public function renderMenu(): string
    {
        return $this->renderer->render($this->prefix."menu");
    }
}
