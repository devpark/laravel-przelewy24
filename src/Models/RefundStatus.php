<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Models;

/**
 * @example
 * 1 - zrealizowany,
 * 2 - oczekuje na realizację,
 * 3 - czeka na akceptację P24,
 * 4 - odrzucony
 */
class RefundStatus
{
    const DONE = 1;
    const WAITING = 2;
    const WAITING_FOR_ACCEPTANCE = 3;
    const REJECTED = 4;
}
