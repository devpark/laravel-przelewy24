<?php

namespace Devpark\Transfers24;

/**
 * Class Channel.
 */
class Channel extends CodeTranslate
{
    const CC = '1';
    const BANK_TRANSFERS = '2';
    const MANUAL_TRANSFER = '4';
    const N_A = '8';
    const ALL_METHOD = '16';
    const PREPAYMENT = '32';

    /**
     * Get Code channel.
     *
     * @param string $channel
     *
     * @return string
     */
    public static function get($channel = self::ALL_METHOD)
    {
        $channel_code = static::getCode($channel, static::ALL_METHOD);

        return $channel_code;
    }
}
