<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
    {
        "data":
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
        "responseCode": 0
    }
 * @property-read RefundInfoData $data
 * @property-read string $responseCode
 */
interface RefundInfoResponseBody
{
}
