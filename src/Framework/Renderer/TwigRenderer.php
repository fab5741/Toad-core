<?php

namespace Framework\Renderer;

use Framework\App;
use Twig_Environment;

/**
 * Class TwigRenderer
 * @package Framework\Renderer
 */
class TwigRenderer implements RendererInterface
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * TwigRenderer constructor.
     * @param Twig_Environment $twig
     * @param App $app
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Add view to namespace
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->twig->getLoader()->addPath("$path", $namespace);
    }

    /**
     * Render a view
     * Namespace can be specified via addPath();
     * $this->render('@blog/view');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.twig', $params);
    }

    /**
     * Add global ariables to all vues
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }

    /**
     * @return Twig_Environment
     */
    public function getTwig(): Twig_Environment
    {
        return $this->twig;
    }
}
