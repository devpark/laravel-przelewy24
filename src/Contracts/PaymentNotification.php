<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

/**
 * @example:
 * {
        "merchantId": 0,
        "posId": 0,
        "sessionId": "string",
        "amount": 0,
        "originAmount": 0,
        "currency": "PLN",
        "orderId": 0,
        "methodId": 0,
        "statement": "string",
        "sign": "string"
    }

 * @property-read  int $merchantId
 * @property-read  int $posId
 * @property-read  string $sessionId
 * @property-read  int $amount
 * @property-read  int $originAmount
 * @property-read  string $currency
 * @property-read  int $orderId
 * @property-read  int $methodId
 * @property-read  string $statement
 * @property-read  string $sign
 **
 */
interface PaymentNotification
{
}
