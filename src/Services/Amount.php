<?php

namespace Devpark\Transfers24\Services;

/**
 * Class Amount.
 */
class Amount
{
    /**
     * Get amount converted into Transfers24 format.
     *
     * @param float $amount
     *
     * @return int
     */
    public static function get($amount)
    {
        if (mb_strpos($amount, ',') != false) {
            $amount = str_replace([','], ['.'], $amount);
        }

        $amount = number_format(round($amount, 2), 2);
        $amount = str_replace(['.', ','], ['', ''], $amount);

        $amount = (int) $amount;

        return $amount;
    }
}
