<?php

namespace Framework\Renderer;

/**
 * Describe how renderers should behave
 *
 * Before render a view should be added to namespace via addPath
 *
 * Interface RendererInterface
 * @package Framework\Renderer
 */
interface RendererInterface
{
    /**
     * Add view to namespace
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void;

    /**
     * Render a view
     * Namespace can be specified via addPath();
     * $this->render('@blog/view');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Add global variables to all vues
     * @param string $key
     * @param mixed $value
     */
    public function addGlobal(string $key, $value): void;
}
