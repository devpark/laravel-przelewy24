<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Models;

/**
 * @example :0 - no payment, 1 - advance payment, 2 - payment made, 3 - payment returned
 */
class TransactionStatus
{
    public const NO_PAYMENT = 0;

    public const ADVANCE_PAYMENT = 1;

    public const PAYMENT_MADE = 2;

    public const PAYMENT_RETURNED = 3;
}
