<?php

namespace Devpark\Transfers24;

/**
 * class Code helper.
 */
abstract class CodeTranslate
{
    const CODE = '-1';

    /**
     * Get helper array of available codes.
     *
     * @return array
     */
    public static function getCodes()
    {
        $codes = [];

        $reflection = new \ReflectionClass(static::class);

        foreach ($reflection->getConstants() as $key => $value) {
            $codes[$key] = $value;
        }

        return $codes;
    }

    /**
     * Get Code for searching value.
     *
     * @param string $name
     * @param string $default_value
     *
     * @return string
     */
    public static function getCode($name, $default_value)
    {
        $codes = static::getCodes();

        if (in_array($name, $codes)) {
            return $name;
        }

        $name = mb_strtoupper(trim($name));
        if (array_key_exists($name, $codes)) {
            return $codes[$name];
        }

        return $default_value;
    }
}
