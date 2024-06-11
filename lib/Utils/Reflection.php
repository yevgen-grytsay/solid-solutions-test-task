<?php

namespace Lib\Utils;

use ReflectionClass;
use ReflectionProperty;

class Reflection
{
    public static function getPublicFields(string|object $class): array
    {
        $reflectionClass = new ReflectionClass($class);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        $publicProperties = [];
        foreach ($properties as $property) {
            $publicProperties[] = $property->getName();
        }

        return $publicProperties;
    }

    /**
     * @template T of object
     *
     * @param T $object
     * @param array $params
     * @return T
     */
    public static function populatePublicFields(object $object, array $params): object
    {
        $fields = self::getPublicFields($object);
        $values = array_intersect_key($params, array_flip($fields));
        foreach ($values as $key => $value) {
            $object->{$key} = $value;
        }

        return $object;
    }
}
