<?php

namespace Framework\Translator;

interface TranslatorInterface
{
    /**
     * Associate a key to a value, for a specified lang
     *
     * @param string $key
     * @param string $lang
     * @param string $value
     * @return mixed
     */
    public function set(string $key, string $lang, string $value);

    /**
     * Get a key to a value, for a specified lang
     *
     * @param string $key
     * @param string $lang
     * @param string $default
     * @return null|String
     */
    public function get(string $key, ? string $lang = "eng", ? string $default = ""): ? String;

    /**
     * Get a key to a value, for a specified lang
     *
     * @param string $key
     * @param string $lang
     * @return mixed
     */
    public function has(string $key, string $lang): bool;
}
