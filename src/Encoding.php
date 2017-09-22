<?php

namespace Devpark\Transfers24;

/**
 * Class Encoding.
 */
class Encoding extends CodeTranslate
{
    const ISO     = 'ISO-8859-2';
    const UTF     = 'UTF-8';
    const WINDOWS = 'Windows-1250';

    /**
     * Get encoding.
     *
     * @param string $encoding
     *
     * @return string
     */
    public static function get($encoding = self::ISO)
    {
        $encodingCode = static::getCode($encoding, static::ISO);

        return $encodingCode;
    }
}
