<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
    {
        "orderId": 0,
        "sessionId": "string",
        "amount": 0,
        "currency": "PLN",
        "refunds":
            [
                {
                "batchId": 0,
                "requestId": "string",
                "date": "string",
                "login": "string",
                "description": "string",
                "status": 3,
                "amount": 0
                }
            ]
    }
 * @property-read int $orderId
 * @property-read string $sessionId
 * @property-read int $amount
 * @property-read string $currency
 * @property-read RefundInfo[] $refunds
 *
 */
interface RefundInfoData
{
}
