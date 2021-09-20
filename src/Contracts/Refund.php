<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
 * {
        "orderId": 0,
        "sessionId": "string",
        "amount": 0,
        "description": "string",
        "status": true,
        "message": "success"
    }
 * @property-read int $orderId
 * @property-read string $sessionId
 * @property-read int $amount
 * @property-read string $description
 * @property-read bool $status
 * @property-read string $message
 *
 */
interface Refund
{
}
