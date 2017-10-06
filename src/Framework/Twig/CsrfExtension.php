<?php

namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;

/**
 * Text extensions for twig
 *
 * Class TextExtension
 * @package Framework\Twig
 */
class CsrfExtension extends \Twig_Extension
{
    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddleware;

    /**
     * FlashExtension constructor.
     * @param CsrfMiddleware $csrfMiddleware
     */
    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('csrf_input', [$this, 'csrf_input'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @return string
     */
    public function csrf_input(): string
    {
        return "<input type=\"hidden\" " .
            "name=\"{$this->csrfMiddleware->getFormKey()}\" " .
            "value=\"{$this->csrfMiddleware->generateToken()}\">";
    }
}
