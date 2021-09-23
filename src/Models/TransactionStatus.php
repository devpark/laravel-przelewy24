<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Models;

/**
 *
 * @example :0 - no payment, 1 - advance payment, 2 - payment made, 3 - payment returned
 */
class TransactionStatus
{
    const NO_PAYMENT = 0;
    const ADVANCE_PAYMENT = 1;
    const PAYMENT_MADE = 2;
    const PAYMENT_RETURNED = 3;
}
