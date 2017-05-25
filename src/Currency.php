<?php

namespace Devpark\Transfers24;

use Devpark\Transfers24\Exceptions\CurrencyException;

/**
 * Class Currency  ISO 4217.
 */
class Currency
{
    const PLN = 'PLN';
    const EUR = 'EUR';
    const GBP = 'GBP';
    const CZK = 'CZK';
    const DKK = 'DKK';

    /**
     * Get helper array of available currencies.
     *
     * @return array
     */
    public static function getCurrencies()
    {
        $currencies = [];

        $reflection = new \ReflectionClass(self::class);

        foreach ($reflection->getConstants() as $key => $value) {
            $currencies[$key] = $value;
        }

        return $currencies;
    }

    /**
     * Get currency.
     *
     * @param string $currency
     *
     * @return string|CurrencyException
     */
    public static function get($currency = self::PLN)
    {
        $currencies = self::getCurrencies();
        $currency = mb_strtoupper(trim($currency));
        if (array_key_exists($currency, $currencies)) {
            return $currencies[$currency];
        }

        throw new CurrencyException('Sorry, currency not supported');
    }
}
