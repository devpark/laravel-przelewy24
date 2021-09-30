<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
    {
        "data":
            [
                {
                    "orderId": 0,
                    "sessionId": "string",
                    "amount": 0,
                    "description": "string",
                    "status": true,
                    "message": "success"
                }
            ],
        "responseCode": 0
    }
 * @property-read Refund[] $data
 * @property-read string $responseCode
 */
interface RefundResponseBody
{
}
