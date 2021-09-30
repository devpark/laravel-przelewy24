<?php

namespace Devpark\Transfers24;

/**
 * Class Channel.
 */
class Channel extends CodeTranslate
{
    public const CC = '1';

    public const BANK_TRANSFERS = '2';

    public const MANUAL_TRANSFER = '4';

    public const N_A = '8';

    public const ALL_METHOD = '16';

    public const PREPAYMENT = '32';

    public const PAY_BY_LINK = '64';

    public const INSTALMENT_PAYMENT = '128';

    public const WALLET = '256';

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
