<?php

namespace Framework\Twig;

use Framework\Module;
use Twig_SimpleFunction;

class MenuTwigExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $modules;

    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction("main_menu", [$this, 'renderMenu'], ['is_safe' => ['html']])
        ];
    }

    public function renderMenu(): string
    {
        return array_reduce($this->modules, function (string $html, Module $module) {
            return $html . $module->renderMenu();
        }, '');
    }
}
