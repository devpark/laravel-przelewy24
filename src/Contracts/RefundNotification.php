<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
 * {
        "orderId": 0,
        "sessionId": "string",
        "merchantId": 0,
        "requestId": "string",
        "refundsUuid": "string",
        "amount": 0,
        "currency": "PLN",
        "timestamp": 0,
        "status": 0,
        "sign": "string"
    }

 * @property-read  int $orderId
 * @property-read  string $sessionId
 * @property-read  int $merchantId
 * @property-read  string $requestId
 * @property-read  string $refundsUuid
 * @property-read  int $amount
 * @property-read  string $currency
 * @property-read  int $timestamp
 * @property-read  int $status
 * @property-read  string $sign
 **
 */
interface RefundNotification
{
}
