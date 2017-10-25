<?php

namespace Framework\Session;

class FlashService implements SessionInterface
{
    /**
     * @var SessionInterface
     */
    private $session;


    private $sessionKey = 'flash';

    private $messages;

    /**
     * FlashService constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function success(string $message, $default = null)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function error(string $message, $default = null)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['error'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    /**
     * @param string $type
     * @param null $default
     * @return null|string
     */
    public function get(string $type, $default = null): ?string
    {
        if (is_null($this->messages)) {
            $this->messages = $this->session->get($this->sessionKey, []);
            $this->session->delete($this->sessionKey);
        }
        if (array_key_exists($type, $this->messages)) {
            return $this->messages[$type];
        }
        return null;
    }

    /**
     * Set info in session
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value): void
    {
        // TODO: Implement set() method.
    }

    /**
     * Delete key in session
     * @param string $key
     */
    public function delete(string $key): void
    {
        // TODO: Implement delete() method.
    }
}
