<?php

namespace Lib\Utils;

class Arr
{
    public static function groupBy(array $array, callable|string $indexFncOrField): array
    {
        $result = [];
        foreach ($array as $k => $v) {
            $key = self::get($v, $indexFncOrField);
            if (!array_key_exists($key, $result)) {
                $result[$key] = [];
            }
            $result[$key][] = $v;
        }

        return $result;
    }
    public static function indexBy(array $array, callable|string $indexFncOrField): array
    {
        if (is_string($indexFncOrField)) {
            // $indexFnc = fn($item) => self::get($item, $indexFncOrField);
            $indexFnc = function($item) use ($indexFncOrField) {
                return self::get($item, $indexFncOrField);
            } ;
        } else {
            $indexFnc = $indexFncOrField;
        }
        $keyList = array_map($indexFnc, $array);

        return array_combine($keyList, $array);
    }

    public static function get(array|object $value, string|int|callable $key, $default = null)
    {
        if (!is_callable($key)) {
            $keyFnc = function (array|object $value) use ($default, $key) {
                if (is_object($value)) {
                    return $value->{$key} ?? $default;
                }

                return $value[$key] ?? $default;
            };
        } else {
            $keyFnc = $key;
        }

        return $keyFnc($value);
    }

    public static function findOne(array $array, callable $predicate, $default = null)
    {
        foreach ($array as $key => $value) {
            if (true === $predicate($value, $key)) {
                return $value;
            }
        }

        return $default;
    }
}
