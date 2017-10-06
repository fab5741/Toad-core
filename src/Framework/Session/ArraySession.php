<?php

namespace Framework\Session;

class ArraySession implements SessionInterface
{
    private $session = [];

    /**
     * Get info in session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
        } else {
            return $default;
        }
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
        $this->session[$key] = $value;
    }

    /**
     * Delete key in session
     * @param string $key
     */
    public function delete(string $key): void
    {
        unset($this->session[$key]);
    }
}
