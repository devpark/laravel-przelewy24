<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
 * {
        "orderId": 0,
        "sessionId": "string",
        "amount": 0,
        "statement": "string",
        "iban": "string",
        "ibanOwner": "string",
        "ibanOwnerAddress": "string"
    }
 * @property-read int $orderId
 * @property-read string $sessionId
 * @property-read int $amount
 * @property-read string $statement
 * @property-read string $iban
 * @property-read string $ibanOwner
 * @property-read string $ibanOwnerAddress
 */
interface RegisterOffline
{
}
