<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Contracts;

use Devpark\Transfers24\Models\TransactionStatus;

/**
 * @example:
 * {
 * "statement": "string",
        * "orderId": 0,
        * "sessionId": "string",
        * "status": 0,
        * "amount": 0,
        * "currency": "PLN",
        * "date": "string",
        * "dateOfTransaction": "string",
        * "clientEmail": "string",
        * "accountMD5": "string",
        * "paymentMethod": 0,
        * "description": "string",
        * "clientName": "string",
        * "clientAddress": "string",
        * "clientCity": "string",
        * "clientPostcode": "string",
        * "batchId": 0,
        * "fee": "string"
    * }
 *
 * @property-read string $statement
 * @property-read int $orderId
 * @property-read string $sessionId
 * @property-read int|TransactionStatus $status
 * @property-read int $amount
 * @property-read string $currency
 * @property-read string $date
 * @property-read string $dateOfTransaction
 * @property-read string $clientEmail
 * @property-read string $accountMD5
 * @property-read int $paymentMethod
 * @property-read string $description
 * @property-read string $clientName
 * @property-read string $clientAddress
 * @property-read string $clientCity
 * @property-read string $clientPostcode
 * @property-read int $batchId
 * @property-read string $fee
 *
 **
 */
interface Transaction
{
}
