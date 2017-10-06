<?php

namespace Framework\Twig;

use Framework\Session\FlashService;

/**
 * Text extensions for twig
 *
 * Class TextExtension
 * @package Framework\Twig
 */
class FlashExtension extends \Twig_Extension
{
    /**
     * @var FlashService
     */
    private $flashService;

    /**
     * FlashExtension constructor.
     * @param FlashService $flashService
     */
    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('flash', [$this, 'getFlash'], ['is_safe' => ['html']])
        ];
    }

    public function getFlash($type): ?string
    {
        return $this->flashService->get($type);
    }
}
