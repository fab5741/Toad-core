<?php

namespace Framework\Translator;

use Framework\Exceptions\TranslateNotFoundException;

class ArrayTranslator
{

    /**
     * var array
     */
    private $storage = [];

    /**
     * Associate a key to a value, for a specified lang
     *
     * @param string $key
     * @param string $lang
     * @param string $value
     * @return mixed
     */
    public function set(string $key, string $lang, string $value)
    {
        $this->storage[$key][$lang] = $value;
    }

    /**
     * Get a key to a value, for a specified lang
     *
     * @param string $key
     * @param string $lang
     * @param string $default
     * @return null|String
     * @throws TranslateNotFoundException
     */
    public function get(string $key, ? string $lang = "eng", ? string $default = null): ? String
    {
        if ($this->has($key, $lang)) {
            return $this->storage[$key][$lang];
        } elseif ($default) {
            return $default;
        } else {
            throw new TranslateNotFoundException("Translate not found");
        }
    }

    /**
     * Test if a key isset for a language
     *
     * @param string $key
     * @param string $lang
     * @return mixed
     */
    public function has(string $key, string $lang): bool
    {
        return array_key_exists($key, $this->storage) && array_key_exists($lang, $this->storage[$key]);
    }
}
