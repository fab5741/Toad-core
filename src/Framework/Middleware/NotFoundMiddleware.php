<?php

namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

/**
 * If No middleware handled that request before, return a 404 message
 *
 * Class NotFoundMiddleware
 * @package Framework\Middlewares
 */
class NotFoundMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        return new Response(404, [], '<h1>Erreur 404</h1>');
    }
}
