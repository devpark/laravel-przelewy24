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
    public const DONE = 1;

    public const WAITING = 2;

    public const WAITING_FOR_ACCEPTANCE = 3;

    public const REJECTED = 4;
}
