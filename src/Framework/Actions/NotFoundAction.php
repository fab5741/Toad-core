<?php

namespace Framework\Actions;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class NotFoundAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * IndexAction constructor.
     * @param RendererInterface $renderer
     */
    public function __construct(
        RendererInterface $renderer
    )
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request)
    {
        return $this->renderer->render('404');
    }
}
