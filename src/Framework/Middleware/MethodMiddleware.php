<?php

namespace Framework\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MethodMiddleware implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $next
     * @return ResponseInterface
     * @internal param DelegateInterface $delegate
     *
     */
    public function process(ServerRequestInterface $request, DelegateInterface $next)
    {
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_method', $parsedBody) &&
            in_array($parsedBody['_method'], ['DELETE', 'PUT'])) {
            $request = $request->withMethod($parsedBody['_method']);
        }
        return $next->process($request);
    }
}
