<?php

namespace Framework\Database;

class Hydrator
{
    public static function hydrate($array, $object)
    {
        $instance = new $object();
        foreach ($array as $key => $value) {
            $method = self::getSetter($key);
            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } else {
                $property = lcfirst(self::getProperty($key));
                $instance->property = $value;
            }
        }
        return $instance;
    }

    public static function getSetter(string $fieldName): string
    {
        return 'set' . self::getProperty($fieldName);
    }

    public static function getProperty(string $fieldName): string
    {
        return join('', array_map('ucfirst', explode('_', $fieldName)));
    }
}
