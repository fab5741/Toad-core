<?php

namespace Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * string
     */
    private $formKey = '_csrf';

    /**
     * @var string
     */
    private $sessionKey = 'csrf';

    /**
     * @var \ArrayAccess
     */
    private $session;

    /**
     * @var int
     */
    private $limit;

    /**
     * CsrfMiddleware constructor.
     * @param array|\ArrayAccess $session
     * @param int $limit
     * @param string $formKey
     * @param string $sessionKey
     */
    public function __construct(&$session, int $limit = 50, string $formKey = '_csrf', string $sessionKey = 'csrf')
    {
        $this->validSession($session);
        $this->session = &$session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError('La session passé au middleware CSRF n\'est pas traitable comme un tableau');
        }
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    return $delegate->process($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $delegate->process($request);
        }
    }

    private function reject()
    {
        throw new CsrfInvalidException();
    }

    private function useToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }

    public function generateToken(): string
    {

        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }

    private function limitTokens()
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * @return mixed
     */
    public function getFormKey()
    {
        return $this->formKey;
    }
}
